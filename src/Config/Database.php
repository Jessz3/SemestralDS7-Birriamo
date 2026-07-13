<?php

declare(strict_types=1);

namespace App\Config;

use PDO;
use PDOException;

/**
 * Clase de conexion a la base de datos usando el patron Singleton.
 * Garantiza una unica instancia de conexion PDO en toda la aplicacion.
 */
final class Database
{
    private static ?Database $instance = null;
    private PDO $connection;

    private function __construct()
    {
        $host = Env::get('DB_HOST', 'localhost');
        $dbName = Env::get('DB_NAME', 'eventos_deportivos');
        $user = Env::get('DB_USER', 'root');
        $password = Env::get('DB_PASSWORD', '');
        $charset = Env::get('DB_CHARSET', 'utf8mb4');

        $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', $host, $dbName, $charset);

        try {
            $this->connection = new PDO($dsn, $user, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            error_log('[DB ERROR] ' . $e->getMessage());
            throw new PDOException('No se pudo establecer conexion con la base de datos.');
        }
    }

    /**
     * Evita clonacion de la instancia (patron Singleton).
     */
    private function __clone()
    {
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }
}
