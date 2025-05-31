<?php
require_once __DIR__ . '/../middleware/cors.php';
require_once __DIR__ . '/../../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

require_once __DIR__ . '/../middleware/jwt_auth.php'; // ✅ Protección con JWT
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../config/middleware.php';

header('Content-Type: application/json');

// Validar método y entrada
validarMetodo('DELETE');
$datos = obtenerJson();
validarCampos($datos, ['id']);

$id = (int) $datos['id'];

try {
    $stmt = $conexion->prepare("DELETE FROM proyectos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    echo json_encode(['status' => 'ok', 'mensaje' => 'Proyecto eliminado correctamente']);
} catch (mysqli_sql_exception $e) {
    error_log('❌ Error SQL: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'mensaje' => 'Error al eliminar el proyecto']);
}
