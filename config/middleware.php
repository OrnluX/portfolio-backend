<?php

function validarMetodo(string $metodoEsperado): void {
    if ($_SERVER['REQUEST_METHOD'] !== strtoupper($metodoEsperado)) {
        http_response_code(405);
        echo json_encode([
            'status' => 'error',
            'mensaje' => "Método no permitido. Se esperaba $metodoEsperado"
        ]);
        exit;
    }
}

function obtenerJson(): array {
    $json = file_get_contents('php://input');
    $datos = json_decode($json, true);

    if (json_last_error() !== JSON_ERROR_NONE || !is_array($datos)) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'mensaje' => 'Cuerpo JSON inválido o mal formado'
        ]);
        exit;
    }

    return $datos;
}

function validarCampos(array $datos, array $camposRequeridos): void {
    foreach ($camposRequeridos as $campo) {
        if (!isset($datos[$campo]) || trim($datos[$campo]) === '') {
            http_response_code(422);
            echo json_encode([
                'status' => 'error',
                'mensaje' => "Campo requerido faltante o vacío: $campo"
            ]);
            exit;
        }
    }
}
