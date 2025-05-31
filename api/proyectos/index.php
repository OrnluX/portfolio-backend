<?php
require_once __DIR__ . '/../middleware/cors.php';
require_once __DIR__ . '/../../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../config/middleware.php';

header('Content-Type: application/json');

// Validar método GET
validarMetodo('GET');

// Consulta a la base de datos
try {
    $sql = "SELECT id, titulo, descripcion, url, fecha_creacion FROM proyectos";
    $resultado = $conexion->query($sql);

    $proyectos = [];

    if ($resultado && $resultado->num_rows > 0) {
        while ($fila = $resultado->fetch_assoc()) {
            $proyectos[] = $fila;
        }
    }

    echo json_encode([
        'status' => 'ok',
        'data' => $proyectos
    ]);
} catch (mysqli_sql_exception $e) {
    error_log('❌ Error SQL: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'mensaje' => 'Error al obtener los proyectos'
    ]);
}
