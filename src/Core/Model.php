<?php

declare(strict_types=1);

namespace App\Core;

use App\Config\Database;
use PDO;

/**
 * Modelo base. Todos los modelos de la aplicacion extienden esta clase
 * para reutilizar la conexion PDO (patron Singleton) sin duplicar codigo.
 */
abstract class Model
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }
}
