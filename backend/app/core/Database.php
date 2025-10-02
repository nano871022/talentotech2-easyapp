<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;
    private static array $env;

    /**
     * Database constructor is private to prevent direct creation of object.
     */
    private function __construct()
    {
    }

    /**
     * Reads the .env file from the project's backend root.
     * Note: A more robust solution would use a library like vlucas/phpdotenv.
     * For this project, a simple parser is sufficient.
     */
    private static function loadEnv(): void
    {
        $path = __DIR__ . '/../../.env';
        if (!isset(self::$env) && file_exists($path)) {
            $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) {
                    continue;
                }

                list($name, $value) = explode('=', $line, 2);
                $name = trim($name);
                $value = trim($value);

                self::$env[$name] = $value;
            }
        }
    }

    /**
     * Gets the single instance of the PDO database connection.
     *
     * @return PDO|null The PDO database connection object.
     */
    public static function getConnection(): ?PDO
    {
        if (self::$instance === null) {
            self::loadEnv();

            $host = self::$env['DB_HOST'] ?? 'localhost';
            $dbname = self::$env['DB_NAME'] ?? '';
            $user = self::$env['DB_USER'] ?? '';
            $pass = self::$env['DB_PASS'] ?? '';
            $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$instance = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                // In a real application, this should be logged, not echoed.
                error_log("Database Connection Error: " . $e->getMessage());
                // Return null or handle the error as per application requirements
                return null;
            }
        }

        return self::$instance;
    }

    /**
     * Prevent cloning of the instance.
     */
    private function __clone()
    {
    }

    /**
     * Prevent unserialization of the instance.
     */
    public function __wakeup()
    {
    }
}