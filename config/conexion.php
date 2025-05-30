<?php
require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

if (!isset($_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASS'], $_ENV['DB_NAME'])) {
    error_log('❌ Variables de entorno incompletas o no cargadas');
    http_response_code(500);
    echo json_encode(['status' => 'error', 'mensaje' => 'Error de configuración del entorno']);
    exit;
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conexion = new mysqli(
        $_ENV['DB_HOST'],
        $_ENV['DB_USER'],
        $_ENV['DB_PASS'],
        $_ENV['DB_NAME']
    );
    $conexion->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    error_log('❌ Error de conexión: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'mensaje' => 'No se pudo conectar a la base de datos']);
    exit;
}
