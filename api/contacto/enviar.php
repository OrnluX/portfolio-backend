<?php
require_once __DIR__ . '/../middleware/cors.php';
require_once __DIR__ . '/../../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

require_once __DIR__ . '/../../config/conexion.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'mensaje' => 'Método no permitido']);
    exit;
}

$datos = json_decode(file_get_contents('php://input'), true);

if (
    !isset($datos['nombre']) || 
    !isset($datos['email']) || 
    !isset($datos['mensaje']) ||
    empty($datos['nombre']) || 
    empty($datos['email']) || 
    empty($datos['mensaje'])
) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'mensaje' => 'Faltan campos obligatorios']);
    exit;
}

$nombre = $conexion->real_escape_string(strip_tags(trim($datos['nombre'])));
$email = filter_var(trim($datos['email']), FILTER_VALIDATE_EMAIL);
$mensaje = $conexion->real_escape_string(strip_tags(trim($datos['mensaje'])));
$ip = $_SERVER['REMOTE_ADDR'];
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido';

if (!$email) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'mensaje' => 'El correo no es válido']);
    exit;
}

// Rate limit: 1 mensaje cada 5 minutos por IP
$consulta = $conexion->prepare("SELECT id FROM mensajes_contacto WHERE ip_origen = ? AND fecha_envio > (NOW() - INTERVAL 5 MINUTE)");
$consulta->bind_param('s', $ip);
$consulta->execute();
$consulta->store_result();

if ($consulta->num_rows > 0) {
    http_response_code(429);
    echo json_encode(['status' => 'error', 'mensaje' => 'Por favor, esperá unos minutos antes de volver a enviar.']);
    exit;
}

// Guardar en la base de datos
$stmt = $conexion->prepare("INSERT INTO mensajes_contacto (nombre, email, mensaje, ip_origen, user_agent) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param('sssss', $nombre, $email, $mensaje, $ip, $userAgent);
$stmt->execute();

if ($stmt->affected_rows <= 0) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'mensaje' => 'Error al guardar en la base de datos']);
    exit;
}

// Enviar el correo
try {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = $_ENV['MAIL_HOST'];
    $mail->SMTPAuth = true;
    $mail->Username = $_ENV['MAIL_USERNAME'];
    $mail->Password = $_ENV['MAIL_PASSWORD'];
    $mail->SMTPSecure = $_ENV['MAIL_ENCRYPTION'] ?? PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = $_ENV['MAIL_PORT'];

    $mail->setFrom($_ENV['MAIL_FROM'], $_ENV['MAIL_FROM_NAME'] ?? 'Portfolio Contacto');
    $mail->addAddress($_ENV['MAIL_TO'] ?? $_ENV['MAIL_FROM']); // destinatario principal
    $mail->addReplyTo($email, $nombre);                         // responder al visitante
    $mail->addAddress($email);                                  // copia al visitante

    $mail->isHTML(true);
    $mail->Subject = '📩 Nuevo mensaje de contacto';
    $mail->Body    = "
        <h3>Nuevo mensaje desde tu portfolio</h3>
        <p><strong>Nombre:</strong> {$nombre}</p>
        <p><strong>Email:</strong> {$email}</p>
        <p><strong>Mensaje:</strong> {$mensaje}</p>
        <p><small>IP de origen: {$ip}</small></p>
    ";

    $mail->send();

    echo json_encode(['status' => 'ok', 'mensaje' => 'Mensaje enviado correctamente']);
} catch (Exception $e) {
    error_log("Error PHPMailer: " . $mail->ErrorInfo);
    http_response_code(500);
    echo json_encode(['status' => 'error', 'mensaje' => 'Error al enviar el correo']);
}


