<?php
require_once __DIR__ . '/../../helpers/jwt.php';

use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use UnexpectedValueException;

header('Content-Type: application/json');

// Obtener el encabezado Authorization: Bearer <token>
$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';

if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'mensaje' => 'Token no proporcionado']);
    exit;
}

$token = $matches[1];

// Verificar y decodificar el token
try {
    $payload = verificarToken($token);
    define('AUTH_PAYLOAD', $payload);

} catch (ExpiredException $e) {
    error_log('⚠️ Token expirado: ' . $e->getMessage());
    http_response_code(401);
    echo json_encode(['status' => 'error', 'mensaje' => 'Tu sesión expiró. Iniciá sesión nuevamente.']);
    exit;

} catch (SignatureInvalidException $e) {
    error_log('❌ Firma inválida: ' . $e->getMessage());
    http_response_code(401);
    echo json_encode(['status' => 'error', 'mensaje' => 'Token inválido.']);
    exit;

} catch (UnexpectedValueException $e) {
    error_log('❌ Token malformado: ' . $e->getMessage());
    http_response_code(401);
    echo json_encode(['status' => 'error', 'mensaje' => 'Token inválido.']);
    exit;

} catch (Exception $e) {
    error_log('❌ Error al verificar token: ' . $e->getMessage());
    http_response_code(401);
    echo json_encode(['status' => 'error', 'mensaje' => 'Error de autenticación.']);
    exit;
}

