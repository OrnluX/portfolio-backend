<?php
// Middleware CORS para permitir peticiones desde el frontend

header('Access-Control-Allow-Origin: *'); // ⚠️ Cambiar a dominio real en producción
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-API-Key, Authorization');

// Responder rápidamente a preflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}
