<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../helpers/jwt.php';

header('Content-Type: application/json');

// Validar método
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'mensaje' => 'Método no permitido']);
    exit;
}

// Obtener JSON
$datos = json_decode(file_get_contents('php://input'), true);

if (!isset($datos['email'], $datos['password']) || empty($datos['email']) || empty($datos['password'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'mensaje' => 'Faltan email o contraseña']);
    exit;
}

$email = filter_var(trim($datos['email']), FILTER_VALIDATE_EMAIL);
$password = trim($datos['password']);

if (!$email) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'mensaje' => 'Correo electrónico no válido']);
    exit;
}

// Buscar usuario en la base de datos
try {
    $stmt = $conexion->prepare("SELECT id, password FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 0) {
        http_response_code(401);
        echo json_encode(['status' => 'error', 'mensaje' => 'Credenciales incorrectas']);
        exit;
    }

    $usuario = $resultado->fetch_assoc();

    if (!password_verify($password, $usuario['password'])) {
        http_response_code(401);
        echo json_encode(['status' => 'error', 'mensaje' => 'Credenciales incorrectas']);
        exit;
    }

    // Generar el token
    $token = crearToken([
        'user_id' => $usuario['id'],
        'email' => $email
    ]);

    echo json_encode(['status' => 'ok', 'token' => $token]);

} catch (mysqli_sql_exception $e) {
    error_log('❌ Error SQL en login: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'mensaje' => 'Error interno del servidor']);
}
