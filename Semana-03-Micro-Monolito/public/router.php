<?php

declare(strict_types=1);

$uri = parse_url(
    $_SERVER['REQUEST_URI'] ?? '/',
    PHP_URL_PATH
);

$uri = is_string($uri) ? $uri : '/';

$file = __DIR__ . $uri;

/*
 * Si se solicita un archivo real, por ejemplo CSS, JS o una imagen,
 * dejamos que el servidor integrado de PHP lo sirva directamente.
 */
if ($uri !== '/' && is_file($file)) {
    return false;
}

/*
 * Las rutas que comienzan con /api/ utilizan la entrada REST.
 */
if (str_starts_with($uri, '/api/')) {
    require __DIR__ . '/api.php';
    return true;
}

/*
 * El resto continúa utilizando la aplicación web existente.
 */
require __DIR__ . '/index.php';

return true;