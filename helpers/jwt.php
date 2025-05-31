<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;

function crearToken(array $payload): string {
    if (!isset($_ENV['JWT_SECRET'])) {
        throw new RuntimeException('JWT_SECRET no está definido en .env');
    }

    $clave = $_ENV['JWT_SECRET'];
    $ahora = time();
    $exp = $ahora + (60 * 60 * 2); // 2 horas de duración

    $payload = array_merge($payload, [
        'iat' => $ahora,
        'exp' => $exp
    ]);

    return JWT::encode($payload, $clave, 'HS256');
}

function verificarToken(string $jwt): object|false {
    if (!isset($_ENV['JWT_SECRET'])) {
        error_log('❌ JWT_SECRET no definido en .env');
        return false;
    }

    $clave = $_ENV['JWT_SECRET'];

    try {
        return JWT::decode($jwt, new Key($clave, 'HS256'));
    } catch (ExpiredException $e) {
        error_log('⚠️ Token expirado: ' . $e->getMessage());
        return false;
    } catch (SignatureInvalidException $e) {
        error_log('❌ Firma inválida: ' . $e->getMessage());
        return false;
    } catch (UnexpectedValueException $e) {
        error_log('❌ Token malformado: ' . $e->getMessage());
        return false;
    } catch (Exception $e) {
        error_log('❌ Error desconocido al verificar token: ' . $e->getMessage());
        return false;
    }
}
