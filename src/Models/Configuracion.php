<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Configuracion extends Model
{
    public function todas(): array
    {
        return $this->db->query('SELECT * FROM configuracion_sistema ORDER BY clave ASC')->fetchAll();
    }

    public function obtener(string $clave, ?string $porDefecto = null): ?string
    {
        $stmt = $this->db->prepare('SELECT valor FROM configuracion_sistema WHERE clave = :clave');
        $stmt->execute(['clave' => $clave]);
        $valor = $stmt->fetchColumn();
        return $valor !== false ? $valor : $porDefecto;
    }

    public function actualizar(string $clave, string $valor): bool
    {
        $stmt = $this->db->prepare('UPDATE configuracion_sistema SET valor = :valor WHERE clave = :clave');
        return $stmt->execute(['valor' => $valor, 'clave' => $clave]);
    }
}
