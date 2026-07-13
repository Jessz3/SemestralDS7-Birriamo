<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class ClaveRsaUsuario extends Model
{
    public function crear(int $usuarioId, string $clavePublica, string $clavePrivadaCifrada, string $huella): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO claves_rsa_usuario (usuario_id, clave_publica, clave_privada_cifrada, huella_publica)
             VALUES (:usuario_id, :clave_publica, :clave_privada_cifrada, :huella_publica)'
        );
        $stmt->execute([
            'usuario_id' => $usuarioId,
            'clave_publica' => $clavePublica,
            'clave_privada_cifrada' => $clavePrivadaCifrada,
            'huella_publica' => $huella,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function activaDeUsuario(int $usuarioId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM claves_rsa_usuario WHERE usuario_id = :usuario_id AND activa = 1
             ORDER BY fecha_creacion DESC LIMIT 1'
        );
        $stmt->execute(['usuario_id' => $usuarioId]);
        return $stmt->fetch() ?: null;
    }
}
