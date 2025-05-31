<?php
require_once __DIR__ . '/../middleware/cors.php';
require_once __DIR__ . '/../../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();


require_once __DIR__ . '/../../config/middleware.php';

header('Content-Type: application/json');

// Validar método
validarMetodo('POST');

require_once __DIR__ . '/../middleware/jwt_auth.php'; // ✅ Protección con JWT
require_once __DIR__ . '/../../config/conexion.php';

// Validar que el usuario tenga permisos para crear proyectos
$datos = obtenerJson();
validarCampos($datos, ['titulo', 'descripcion', 'url']);

try {
    $stmt = $conexion->prepare("INSERT INTO proyectos (titulo, descripcion, url) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $datos['titulo'], $datos['descripcion'], $datos['url']);
    $stmt->execute();

    echo json_encode(['status' => 'ok', 'mensaje' => 'Proyecto creado correctamente']);
} catch (mysqli_sql_exception $e) {
    error_log('❌ Error SQL: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'mensaje' => 'Error al insertar el proyecto']);
}
