<?php

declare(strict_types=1);

use MicroHis\Application\UseCase\ChangeMedicationStatus;
use MicroHis\Application\UseCase\GetMedication;
use MicroHis\Application\UseCase\RegisterMedication;
use MicroHis\Application\UseCase\SearchMedications;
use MicroHis\Persistence\DatabaseConnection;
use MicroHis\Persistence\PdoMedicationRepository;
use MicroHis\Presentation\MedicationApiController;

require dirname(__DIR__) . '/src/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');

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

    $controller = new MedicationApiController(
        new RegisterMedication($repository),
        new SearchMedications($repository),
        new GetMedication($repository),
        new ChangeMedicationStatus($repository),
    );

    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    $uri = parse_url(
        $_SERVER['REQUEST_URI'] ?? '/',
        PHP_URL_PATH
    );

    $uri = is_string($uri) ? $uri : '/';

    $input = [];

    if (in_array($method, ['POST', 'PATCH'], true)) {
        $rawBody = file_get_contents('php://input');

        if ($rawBody !== false && trim($rawBody) !== '') {
            $decoded = json_decode($rawBody, true);

            if (!is_array($decoded)) {
                http_response_code(400);

                echo json_encode(
                    [
                        'error' => [
                            'code' => 'INVALID_JSON',
                            'message' => 'El cuerpo de la solicitud contiene JSON inválido.',
                        ],
                    ],
                    JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
                );

                exit;
            }

            $input = $decoded;
        }
    }

    $result = null;

    if (
        $method === 'GET'
        && $uri === '/api/v1/medications'
    ) {
        $search = $_GET['search'] ?? '';

        $result = $controller->index(
            is_scalar($search) ? (string) $search : ''
        );
    }

    if (
        $method === 'GET'
        && preg_match(
            '#^/api/v1/medications/(\d+)$#',
            $uri,
            $matches
        ) === 1
    ) {
        $result = $controller->show((int) $matches[1]);
    }

    if (
        $method === 'POST'
        && $uri === '/api/v1/medications'
    ) {
        $result = $controller->store($input);
    }

    if (
        $method === 'PATCH'
        && preg_match(
            '#^/api/v1/medications/(\d+)/status$#',
            $uri,
            $matches
        ) === 1
    ) {
        $result = $controller->updateStatus(
            (int) $matches[1],
            $input
        );
    }

    if ($result === null) {
        $result = [
            'status' => 404,
            'body' => [
                'error' => [
                    'code' => 'ROUTE_NOT_FOUND',
                    'message' => 'La ruta solicitada no existe.',
                ],
            ],
        ];
    }

    http_response_code($result['status']);

    echo json_encode(
        $result['body'],
        JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
    );
} catch (Throwable $exception) {
    http_response_code(500);

    echo json_encode(
        [
            'error' => [
                'code' => 'INTERNAL_ERROR',
                'message' => 'No fue posible iniciar la API.',
            ],
        ],
        JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
    );
}