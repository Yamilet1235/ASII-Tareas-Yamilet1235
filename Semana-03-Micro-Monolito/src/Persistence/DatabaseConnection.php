<?php

declare(strict_types=1);

namespace MicroHis\Persistence;

use MicroHis\Domain\Exception\RepositoryException;
use PDO;
use PDOException;

final class DatabaseConnection
{
    /** @param array{path?: string} $config */
    public static function create(array $config): PDO
    {
        $path = $config['path'] ?? '';

        if (!is_string($path) || trim($path) === '') {
            throw new RepositoryException('La ruta de la base de datos no está configurada.');
        }

        try {
            return new PDO('sqlite:' . $path, null, null, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $exception) {
            throw new RepositoryException(
                'No fue posible conectar con la base de datos SQLite.',
                0,
                $exception,
            );
        }
    }
}
