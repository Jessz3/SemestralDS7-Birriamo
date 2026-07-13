<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class HistorialPassword extends Model
{
    public function registrar(int $usuarioId, string $passwordHash): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO historial_passwords (usuario_id, password_hash) VALUES (:usuario_id, :password_hash)'
        );
        $stmt->execute(['usuario_id' => $usuarioId, 'password_hash' => $passwordHash]);
    }
}
