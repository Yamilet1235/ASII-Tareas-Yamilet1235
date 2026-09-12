<?php

declare(strict_types=1);

use MicroHis\Application\UseCase\RegisterMedication;
use MicroHis\Application\UseCase\SearchMedications;
use MicroHis\Application\UseCase\ToggleMedicationStatus;
use MicroHis\Persistence\DatabaseConnection;
use MicroHis\Persistence\PdoMedicationRepository;
use MicroHis\Presentation\MedicationController;

require dirname(__DIR__) . '/src/bootstrap.php';

session_start();

try {
    $configFile = dirname(__DIR__) . '/config/config.php';
    if (!is_file($configFile)) {
        throw new RuntimeException(
            'Falta config/config.php. Créelo a partir de config/config.example.php.'
        );
    }

    $config = require $configFile;
    $pdo = DatabaseConnection::create($config['database'] ?? []);
    $repository = new PdoMedicationRepository($pdo);
    $controller = new MedicationController(
        new RegisterMedication($repository),
        new SearchMedications($repository),
        new ToggleMedicationStatus($repository),
    );

    $viewData = $controller->handle($_SERVER, $_GET, $_POST);

    if ($viewData['redirect'] === true) {
        header('Location: /', true, 303);
        exit;
    }

    extract($viewData, EXTR_SKIP);
    require dirname(__DIR__) . '/src/Presentation/views/catalog.php';
} catch (Throwable $exception) {
    http_response_code(500);
    $message = htmlspecialchars($exception->getMessage(), ENT_QUOTES, 'UTF-8');
    echo '<!doctype html><html lang="es"><meta charset="utf-8">';
    echo '<title>Error de configuración</title>';
    echo '<main style="font-family:sans-serif;max-width:720px;margin:4rem auto;padding:1rem">';
    echo '<h1>No se pudo iniciar Micro-HIS</h1><p>' . $message . '</p>';
    echo '<p>Revise la configuración e inicialice la base de datos.</p></main></html>';
}
