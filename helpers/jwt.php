<?php

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function crearToken(array $payload): string {
    if (!isset($_ENV['JWT_SECRET'])) {
        throw new RuntimeException('JWT_SECRET no está definido en .env');
    }

    $clave = $_ENV['JWT_SECRET'];
    $ahora = time();
    $exp = $ahora + (60 * 60 * 2); // 2 horas de duración

    $payload = array_merge($payload, [
        'iat' => $ahora,
        'exp' => $exp
    ]);

    return JWT::encode($payload, $clave, 'HS256');
}

function verificarToken(string $jwt): object {
    if (!isset($_ENV['JWT_SECRET'])) {
        throw new RuntimeException('JWT_SECRET no está definido en .env');
    }

    $clave = $_ENV['JWT_SECRET'];
    return JWT::decode($jwt, new Key($clave, 'HS256'));
}
