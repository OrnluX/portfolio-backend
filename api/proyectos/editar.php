<?php
require_once __DIR__ . '/../middleware/cors.php';
require_once __DIR__ . '/../../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();


require_once __DIR__ . '/../../config/middleware.php';

header('Content-Type: application/json');

// Validar método 
validarMetodo('PUT');

require_once __DIR__ . '/../middleware/jwt_auth.php'; // ✅ Protección con JWT
require_once __DIR__ . '/../../config/conexion.php';

// Validar que el usuario tenga permisos para editar proyectos
$datos = obtenerJson();
validarCampos($datos, ['id', 'titulo', 'descripcion', 'url']);

$id = (int) $datos['id'];

try {
    $stmt = $conexion->prepare("UPDATE proyectos SET titulo = ?, descripcion = ?, url = ? WHERE id = ?");
    $stmt->bind_param("sssi", $datos['titulo'], $datos['descripcion'], $datos['url'], $id);
    $stmt->execute();

    echo json_encode(['status' => 'ok', 'mensaje' => 'Proyecto actualizado correctamente']);
} catch (mysqli_sql_exception $e) {
    error_log('❌ Error SQL: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'mensaje' => 'Error al actualizar el proyecto']);
}
