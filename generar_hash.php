<?php
// Solo para crear el hash una vez
$hash = password_hash("MiContraseñaSegura123", PASSWORD_DEFAULT);
echo $hash;
