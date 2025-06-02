<?php
require_once __DIR__ . '/../middleware/cors.php';
require_once __DIR__ . '/../middleware/jwt_auth.php';

header('Content-Type: application/json');

echo json_encode([
  'status' => 'ok',
  'mensaje' => 'Token válido',
  'usuario' => [
        'id' => $_AUTH_PAYLOAD->user_id ?? null,
        'email' => $_AUTH_PAYLOAD->email ?? null
    ]
]);
