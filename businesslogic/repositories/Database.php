<?php

class Database
{
    private static ?PDO $instance = null;

    private function __construct() {}
    private function __clone() {}

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            // Cargar credenciales de forma segura desde variables de entorno.
            // Estos valores deben configurarse en el servidor (ej. panel de InfinityFree, CPanel, etc.)
            // o en un archivo .env que no se sube al repositorio.
            $db_host = getenv('DB_HOST') ?: 'sql213.infinityfree.com'; // Fallback para ejemplo
            $db_name = getenv('DB_NAME') ?: 'if0_40011443_easyapp';
            $db_user = getenv('DB_USER') ?: 'if0_40011443';
            $db_pass = getenv('DB_PASS') ?: 'YOUR_DB_PASSWORD'; // La contraseña no debe tener fallback

            if (!$db_pass) {
                error_log('Database password environment variable (DB_PASS) not set.');
                die('Error: Configuración de base de datos incompleta.');
            }

            $dsn = "mysql:host={$db_host};dbname={$db_name};charset=utf8mb4";

            try {
                self::$instance = new PDO($dsn, $db_user, $db_pass);
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                error_log('Database Connection Error: ' . $e->getMessage());
                die('Error: No se pudo conectar a la base de datos.');
            }
        }
        return self::$instance;
    }
}