<?php

declare(strict_types=1);

namespace MicroHis\Persistence;

use MicroHis\Domain\Exception\DuplicateMedicationException;
use MicroHis\Domain\Exception\RepositoryException;
use MicroHis\Domain\Medication;
use MicroHis\Domain\MedicationNameNormalizer;
use MicroHis\Domain\MedicationStatus;
use MicroHis\Domain\Repository\MedicationRepository;
use PDO;
use PDOException;

final class PdoMedicationRepository implements MedicationRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function add(Medication $medication): Medication
    {
        try {
            $statement = $this->pdo->prepare(
                'INSERT INTO medications
                    (name, normalized_name, generic_name, presentation, concentration, status)
                 VALUES
                    (:name, :normalized_name, :generic_name, :presentation, :concentration, :status)'
            );
            $statement->execute([
                ':name' => $medication->name(),
                ':normalized_name' => MedicationNameNormalizer::normalize($medication->name()),
                ':generic_name' => $medication->genericName(),
                ':presentation' => $medication->presentation(),
                ':concentration' => $medication->concentration(),
                ':status' => $medication->status()->value,
            ]);

            return new Medication(
                (int) $this->pdo->lastInsertId(),
                $medication->name(),
                $medication->genericName(),
                $medication->presentation(),
                $medication->concentration(),
                $medication->status(),
            );
        } catch (PDOException $exception) {
            if ($this->isUniqueViolation($exception)) {
                throw new DuplicateMedicationException(
                    'Ya existe un medicamento con ese nombre.',
                    0,
                    $exception,
                );
            }

            throw new RepositoryException(
                'No se pudo guardar el medicamento en la base de datos.',
                0,
                $exception,
            );
        }
    }

    public function findById(int $id): ?Medication
    {
        try {
            $statement = $this->pdo->prepare(
                'SELECT id, name, generic_name, presentation, concentration, status
                 FROM medications
                 WHERE id = :id'
            );
            $statement->execute([':id' => $id]);
            $row = $statement->fetch();

            return $row === false ? null : $this->hydrate($row);
        } catch (PDOException $exception) {
            throw new RepositoryException(
                'No se pudo consultar el medicamento.',
                0,
                $exception,
            );
        }
    }

    public function findByName(string $name): ?Medication
    {
        try {
            $statement = $this->pdo->prepare(
                'SELECT id, name, generic_name, presentation, concentration, status
                 FROM medications
                 WHERE normalized_name = :normalized_name
                 LIMIT 1'
            );
            $statement->execute([
                ':normalized_name' => MedicationNameNormalizer::normalize($name),
            ]);
            $row = $statement->fetch();

            return $row === false ? null : $this->hydrate($row);
        } catch (PDOException $exception) {
            throw new RepositoryException(
                'No se pudo verificar el nombre del medicamento.',
                0,
                $exception,
            );
        }
    }

    public function search(string $query): array
    {
        try {
            $statement = $this->pdo->prepare(
                'SELECT id, name, generic_name, presentation, concentration, status
                 FROM medications
                 WHERE normalized_name LIKE :name_query
                    OR generic_name LIKE :generic_query COLLATE NOCASE
                 ORDER BY name COLLATE NOCASE ASC'
            );
            $pattern = '%' . trim($query) . '%';
            $statement->execute([
                ':name_query' => '%' . MedicationNameNormalizer::normalize($query) . '%',
                ':generic_query' => $pattern,
            ]);

            $medications = [];
            foreach ($statement->fetchAll() as $row) {
                $medications[] = $this->hydrate($row);
            }

            return $medications;
        } catch (PDOException $exception) {
            throw new RepositoryException(
                'No se pudo buscar en el catálogo de medicamentos.',
                0,
                $exception,
            );
        }
    }

    public function changeStatus(int $id, MedicationStatus $status): Medication
    {
        try {
            $statement = $this->pdo->prepare(
                'UPDATE medications SET status = :status WHERE id = :id'
            );
            $statement->execute([
                ':status' => $status->value,
                ':id' => $id,
            ]);

            $medication = $this->findById($id);
            if ($medication === null) {
                throw new RepositoryException('El medicamento ya no existe.');
            }

            return $medication;
        } catch (PDOException $exception) {
            throw new RepositoryException(
                'No se pudo actualizar el estado del medicamento.',
                0,
                $exception,
            );
        }
    }

    /** @param array<string, mixed> $row */
    private function hydrate(array $row): Medication
    {
        return new Medication(
            (int) $row['id'],
            (string) $row['name'],
            (string) $row['generic_name'],
            (string) $row['presentation'],
            (string) $row['concentration'],
            MedicationStatus::fromInput((string) $row['status']),
        );
    }

    private function isUniqueViolation(PDOException $exception): bool
    {
        return ($exception->errorInfo[1] ?? null) === 19
            && str_contains(strtolower($exception->getMessage()), 'unique');
    }
}
