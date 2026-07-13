<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class MensajeContacto extends Model
{
    public function todos(): array
    {
        return $this->db->query('SELECT * FROM mensajes_contacto ORDER BY fecha_envio DESC')->fetchAll();
    }

    public function crear(array $datos): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO mensajes_contacto (nombre, correo, telefono, asunto, mensaje)
             VALUES (:nombre, :correo, :telefono, :asunto, :mensaje)'
        );
        $stmt->execute([
            'nombre' => $datos['nombre'],
            'correo' => $datos['correo'],
            'telefono' => $datos['telefono'] ?? null,
            'asunto' => $datos['asunto'],
            'mensaje' => $datos['mensaje'],
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function marcarLeido(int $id, int $usuarioId): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE mensajes_contacto SET estado = 'LEIDO', atendido_por = :usuario_id, fecha_atencion = NOW()
             WHERE id = :id AND estado = 'NUEVO'"
        );
        return $stmt->execute(['usuario_id' => $usuarioId, 'id' => $id]);
    }
}
