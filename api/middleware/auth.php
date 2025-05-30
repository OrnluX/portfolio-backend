<?php
// Middleware para proteger la API con un token secreto (API Key)

if (!isset($_SERVER['HTTP_X_API_KEY']) || $_SERVER['HTTP_X_API_KEY'] !== $_ENV['API_SECRET']) {
    http_response_code(401); // Unauthorized
    echo json_encode(['status' => 'error', 'mensaje' => 'No autorizado']);
    exit;
}
