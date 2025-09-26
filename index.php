<?php
// index.php
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Registro - Asesoría Inglés</title>
</head>
<body>
  <h1>Regístrate para recibir asesoría</h1>
  <form action="register_process.php" method="post">
    <label>Nombre completo<br><input type="text" name="nombre" required maxlength="200"></label><br>
    <label>Correo electrónico<br><input type="email" name="correo" required maxlength="255"></label><br>
    <label>Teléfono<br><input type="text" name="telefono" maxlength="50"></label><br>
    <label>Preferencia de contacto
      <select name="preferencia_contacto">
        <option value="email">Email</option>
        <option value="telefono">Teléfono</option>
      </select>
    </label><br>
    <label><input type="checkbox" name="consentimiento" value="1" required> Acepto la política de tratamiento de datos</label><br>
    <button type="submit">Enviar solicitud</button>
  </form>
</body>
</html>
