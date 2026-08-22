<?php

declare(strict_types=1);

use MicroHis\Persistence\DatabaseConnection;
use MicroHis\Domain\MedicationNameNormalizer;

require dirname(__DIR__) . '/src/bootstrap.php';

$configFile = dirname(__DIR__) . '/config/config.php';
if (!is_file($configFile)) {
    fwrite(STDERR, "Error: falta config/config.php. Créelo desde config/config.example.php.\n");
    exit(1);
}

try {
    $config = require $configFile;
    $pdo = DatabaseConnection::create($config['database'] ?? []);

    $schema = file_get_contents(dirname(__DIR__) . '/database/schema.sql');
    $seedStatement = file_get_contents(dirname(__DIR__) . '/database/seed.sql');
    if ($schema === false || $seedStatement === false) {
        throw new RuntimeException('No fue posible leer los archivos SQL.');
    }

    $pdo->prepare($schema)->execute();
    $insert = $pdo->prepare($seedStatement);

    $fictitiousMedications = [
        ['Alivion', 'analgésico ficticio A', 'Tabletas', '250 mg', 'ACTIVE'],
        ['Respiralux', 'compuesto respiratorio ficticio B', 'Jarabe', '100 mg/5 ml', 'ACTIVE'],
        ['Dermacalm X', 'compuesto dérmico ficticio C', 'Crema', '2 %', 'INACTIVE'],
    ];

    foreach ($fictitiousMedications as [$name, $genericName, $presentation, $concentration, $status]) {
        $insert->execute([
            ':name' => $name,
            ':normalized_name' => MedicationNameNormalizer::normalize($name),
            ':generic_name' => $genericName,
            ':presentation' => $presentation,
            ':concentration' => $concentration,
            ':status' => $status,
        ]);
    }

    $count = $pdo->prepare('SELECT COUNT(*) FROM medications');
    $count->execute();

    echo "Base de datos inicializada correctamente.\n";
    echo 'Medicamentos disponibles: ' . $count->fetchColumn() . "\n";
} catch (Throwable $exception) {
    fwrite(STDERR, 'Error al inicializar: ' . $exception->getMessage() . "\n");
    exit(1);
}
