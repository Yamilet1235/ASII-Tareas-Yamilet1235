<?php

declare(strict_types=1);

use MicroHis\Application\Exception\ApplicationException;
use MicroHis\Application\UseCase\RegisterMedication;
use MicroHis\Application\UseCase\SearchMedications;
use MicroHis\Application\UseCase\ToggleMedicationStatus;
use MicroHis\Domain\Exception\DuplicateMedicationException;
use MicroHis\Domain\Exception\RepositoryException;
use MicroHis\Domain\Exception\ValidationException;
use MicroHis\Domain\Medication;
use MicroHis\Domain\MedicationStatus;
use MicroHis\Domain\Repository\MedicationRepository;
use MicroHis\Persistence\DatabaseConnection;
use MicroHis\Persistence\InMemoryMedicationRepository;
use MicroHis\Persistence\PdoMedicationRepository;
use MicroHis\Presentation\MedicationWebRequest;

require dirname(__DIR__) . '/src/bootstrap.php';

final class FailingMedicationRepository implements MedicationRepository
{
    private function fail(): never
    {
        throw new RepositoryException(
            'Falla simulada de persistencia.'
        );
    }

    public function add(Medication $medication): Medication
    {
        return $this->fail();
    }

    public function findById(int $id): ?Medication
    {
        return $this->fail();
    }

    public function findByName(string $name): ?Medication
    {
        return $this->fail();
    }

    public function search(string $query): array
    {
        return $this->fail();
    }

    public function changeStatus(
        int $id,
        MedicationStatus $status
    ): Medication {
        return $this->fail();
    }
}

/** @var array<string, Closure(): void> $tests */
$tests = [];

$tests['Camino feliz: registra un medicamento válido'] =
    static function (): void {
        $useCase = new RegisterMedication(
            new InMemoryMedicationRepository()
        );

        $medication = $useCase->execute(
            'Vitalex',
            'compuesto ficticio',
            'Cápsulas',
            '10 mg',
            'ACTIVE'
        );

        assertTrue(
            $medication->id() === 1,
            'El registro debe recibir un identificador.'
        );

        assertTrue(
            $medication->name() === 'Vitalex',
            'El nombre debe conservarse.'
        );
    };

$tests['Dominio: rechaza campos obligatorios vacíos'] =
    static function (): void {
        assertThrows(
            ValidationException::class,
            static fn (): Medication => Medication::register(
                '',
                'genérico',
                'Tabletas',
                '5 mg',
                'ACTIVE'
            )
        );
    };

$tests['Aplicación: rechaza duplicados con mayúsculas y acentos'] =
    static function (): void {
        $repository = new InMemoryMedicationRepository();
        $useCase = new RegisterMedication($repository);

        $useCase->execute(
            'Ácido Vital',
            'compuesto A',
            'Tabletas',
            '250 mg',
            'ACTIVE'
        );

        assertThrows(
            DuplicateMedicationException::class,
            static fn (): Medication => $useCase->execute(
                'ácido vital',
                'otro compuesto',
                'Jarabe',
                '10 mg/ml',
                'ACTIVE'
            )
        );
    };

$tests['Vigencia: un medicamento inactivo no es vigente ni seleccionable'] =
    static function (): void {
        $medication = Medication::register(
            'Dormex Ficticio',
            'compuesto D',
            'Gotas',
            '1 mg/ml',
            'INACTIVE'
        );

        assertTrue(
            !$medication->isCurrent(),
            'El medicamento INACTIVE no debe estar vigente.'
        );

        assertTrue(
            !$medication->isSelectable(),
            'El medicamento INACTIVE no debe ser seleccionable.'
        );
    };

$tests['Aplicación: transforma un error de persistencia'] =
    static function (): void {
        $useCase = new RegisterMedication(
            new FailingMedicationRepository()
        );

        assertThrows(
            ApplicationException::class,
            static fn (): Medication => $useCase->execute(
                'Prueba Ficticia',
                'compuesto E',
                'Solución',
                '3 mg/ml',
                'ACTIVE'
            )
        );
    };

$tests['Persistencia PDO: integra alta, búsqueda, unicidad y estado'] =
    static function (): void {
        $pdo = DatabaseConnection::create([
            'path' => ':memory:',
        ]);

        $schema = file_get_contents(
            dirname(__DIR__) . '/database/schema.sql'
        );

        assertTrue(
            $schema !== false,
            'El esquema SQL debe estar disponible.'
        );

        $pdo->prepare($schema)->execute();

        $repository = new PdoMedicationRepository($pdo);
        $register = new RegisterMedication($repository);
        $search = new SearchMedications($repository);
        $toggle = new ToggleMedicationStatus($repository);

        $saved = $register->execute(
            'Ácido Demo',
            'compuesto ficticio F',
            'Tabletas',
            '1 mg',
            'ACTIVE'
        );

        assertThrows(
            DuplicateMedicationException::class,
            static fn (): Medication => $register->execute(
                'ácido demo',
                'otro compuesto ficticio',
                'Jarabe',
                '2 mg/ml',
                'ACTIVE'
            )
        );

        assertTrue(
            count($search->execute('ácido')) === 1,
            'La búsqueda debe encontrar el nombre comercial.'
        );

        assertTrue(
            count(
                $search->execute('COMPUESTO FICTICIO F')
            ) === 1,
            'La búsqueda debe encontrar el nombre genérico.'
        );

        $changed = $toggle->execute(
            (int) $saved->id()
        );

        assertTrue(
            !$changed->isCurrent(),
            'El repositorio debe guardar el estado INACTIVE.'
        );
    };

$tests['Presentation: interpreta método, acción e identificador web'] =
    static function (): void {
        $request = new MedicationWebRequest(
            ['REQUEST_METHOD' => ' post '],
            [],
            ['action' => ' toggle ', 'id' => '17']
        );

        assertTrue(
            $request->method() === 'POST',
            'El método HTTP debe normalizarse en mayúsculas.'
        );

        assertTrue(
            $request->action() === 'toggle',
            'La acción debe obtenerse sin espacios externos.'
        );

        assertTrue(
            $request->id() === 17,
            'El identificador debe convertirse a entero.'
        );
    };

$tests['Presentation: normaliza el término de búsqueda web'] =
    static function (): void {
        $request = new MedicationWebRequest(
            [],
            ['q' => '  compuesto ficticio  '],
            []
        );

        assertTrue(
            $request->search() === 'compuesto ficticio',
            'La búsqueda debe excluir espacios externos.'
        );
    };

$tests['Presentation: prepara formulario y estado ACTIVE por defecto'] =
    static function (): void {
        $request = new MedicationWebRequest(
            [],
            [],
            [
                'name' => 'Vitalex Web',
                'generic_name' => 'compuesto ficticio web',
                'presentation' => 'Tabletas',
                'concentration' => '20 mg',
            ]
        );

        assertTrue(
            $request->medicationForm() === [
                'name' => 'Vitalex Web',
                'generic_name' => 'compuesto ficticio web',
                'presentation' => 'Tabletas',
                'concentration' => '20 mg',
                'status' => 'ACTIVE',
            ],
            'El formulario debe conservar los campos y usar ACTIVE por defecto.'
        );
    };

function assertTrue(
    bool $condition,
    string $message
): void {
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

/**
 * @param class-string<Throwable> $expected
 */
function assertThrows(
    string $expected,
    callable $callback
): void {
    try {
        $callback();
    } catch (Throwable $exception) {
        if ($exception instanceof $expected) {
            return;
        }

        throw new RuntimeException(
            sprintf(
                'Se esperaba %s, pero se recibió %s.',
                $expected,
                $exception::class
            )
        );
    }

    throw new RuntimeException(
        'Se esperaba una excepción de tipo '
        . $expected
        . '.'
    );
}

echo "Micro-HIS Catálogo de Medicamentos - Pruebas\n";
echo str_repeat('=', 49) . "\n";

$passed = 0;
$failed = 0;

foreach ($tests as $name => $test) {
    try {
        $test();

        $passed++;

        echo "[APROBADA] {$name}\n";
    } catch (Throwable $exception) {
        $failed++;

        echo "[FALLIDA]  {$name}\n";
        echo "           {$exception->getMessage()}\n";
    }
}

echo str_repeat('-', 49) . "\n";
echo "Resultado: {$passed} aprobadas, {$failed} fallidas.\n";

exit($failed === 0 ? 0 : 1);
