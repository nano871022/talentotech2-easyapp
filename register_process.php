<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__.'/includes/db.php';
require_once __DIR__.'/includes/config.php';
require_once __DIR__.'/includes/functions.php';

$nombre = trim($_POST['nombre'] ?? '');
$correo = trim($_POST['correo'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$preferencia = in_array($_POST['preferencia_contacto'] ?? 'email', ['email','telefono']) ? $_POST['preferencia_contacto'] : 'email';
$consentimiento = isset($_POST['consentimiento']) && $_POST['consentimiento'] == '1' ? 1 : 0;

if (empty($nombre) || empty($correo) || !$consentimiento) {
    die('Datos incompletos o sin consentimiento.');
}

// Validaciones básicas
if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    die('Correo inválido.');
}

// Insertar o actualizar (upsert para evitar duplicados de correo)
$stmt = $mysqli->prepare("INSERT INTO contactos (nombre, correo, telefono, preferencia_contacto, consentimiento, baja_token, update_token) VALUES (?, ?, ?, ?, ?, ?, ?)
  ON DUPLICATE KEY UPDATE nombre=VALUES(nombre), telefono=VALUES(telefono), preferencia_contacto=VALUES(preferencia_contacto), consentimiento=VALUES(consentimiento), updated_at=NOW()");
$baja_token = generate_token();
$update_token = generate_token();
$stmt->bind_param('sssiiss', $nombre, $correo, $telefono, $preferencia, $consentimiento, $baja_token, $update_token);
if (!$stmt->execute()) {
    die('Error al registrar: '.$stmt->error);
}

// Enviar email con tokens (o mostrar token en pantalla para prueba)
$subject = "Confirmación de registro en Easy App" ;
$baja_url = "https://cursoingles.gt.tc/unsubscribe.php?token=".$baja_token;
$update_url = "https://cursoingles.gt.tc/update_request.php?token=".$update_token;
$body = "Hola $nombre,\n\nGracias por registrarte. Si quieres darte de baja usa este enlace:\n$baja_url\n\nSi deseas actualizar tus datos:\n$update_url\n\nSi estás en entorno de prueba y el correo no llega, conserva los tokens mostrados en la confirmación en pantalla.\n\nSaludos.";

$sent = send_token_email($correo, $subject, $body);

// Respuesta al usuario
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Registro exitoso</title></head>
<body>
  <h1>Registro exitoso</h1>
  <p>Gracias <?=htmlspecialchars($nombre)?>. Te contactaremos según tu preferencia.</p>
  <?php if (!$sent): ?>
    <p><strong>Nota:</strong> el sistema no pudo enviar correo desde este hospedaje. Usa estos enlaces (prueba):</p>
    <ul>
      <li>Dar de baja: <a href="<?=$baja_url?>"><?=$baja_url?></a></li>
      <li>Solicitar actualización: <a href="<?=$update_url?>"><?=$update_url?></a></li>
    </ul>
  <?php else: ?>
    <p>Se envió un correo de confirmación a <?=$correo?></p>
  <?php endif; ?>
</body>
</html>
