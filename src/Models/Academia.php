<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Academia extends Model
{
    public function todos(): array
    {
        return $this->db->query('SELECT * FROM academias ORDER BY nombre ASC')->fetchAll();
    }

    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM academias WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function deportesDeAcademia(int $academiaId): array
    {
        $stmt = $this->db->prepare(
            'SELECT d.* FROM academia_deportes ad
             JOIN deportes d ON d.id = ad.deporte_id
             WHERE ad.academia_id = :academia_id
             ORDER BY d.nombre'
        );
        $stmt->execute(['academia_id' => $academiaId]);
        return $stmt->fetchAll();
    }

    public function crear(array $datos): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO academias (nombre, ruc, descripcion, correo, telefono, direccion, activo)
             VALUES (:nombre, :ruc, :descripcion, :correo, :telefono, :direccion, :activo)'
        );
        $stmt->execute([
            'nombre' => $datos['nombre'],
            'ruc' => $datos['ruc'] ?: null,
            'descripcion' => $datos['descripcion'] ?? null,
            'correo' => $datos['correo'] ?: null,
            'telefono' => $datos['telefono'] ?? null,
            'direccion' => $datos['direccion'] ?? null,
            'activo' => $datos['activo'] ?? 1,
        ]);
        $id = (int) $this->db->lastInsertId();
        $this->sincronizarDeportes($id, $datos['deportes'] ?? []);
        return $id;
    }

    public function actualizar(int $id, array $datos): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE academias SET nombre = :nombre, ruc = :ruc, descripcion = :descripcion,
             correo = :correo, telefono = :telefono, direccion = :direccion WHERE id = :id'
        );
        $resultado = $stmt->execute([
            'nombre' => $datos['nombre'],
            'ruc' => $datos['ruc'] ?: null,
            'descripcion' => $datos['descripcion'] ?? null,
            'correo' => $datos['correo'] ?: null,
            'telefono' => $datos['telefono'] ?? null,
            'direccion' => $datos['direccion'] ?? null,
            'id' => $id,
        ]);
        $this->sincronizarDeportes($id, $datos['deportes'] ?? []);
        return $resultado;
    }

    private function sincronizarDeportes(int $academiaId, array $deporteIds): void
    {
        $stmt = $this->db->prepare('DELETE FROM academia_deportes WHERE academia_id = :academia_id');
        $stmt->execute(['academia_id' => $academiaId]);

        if (empty($deporteIds)) {
            return;
        }

        $insertar = $this->db->prepare(
            'INSERT INTO academia_deportes (academia_id, deporte_id) VALUES (:academia_id, :deporte_id)'
        );
        foreach ($deporteIds as $deporteId) {
            $insertar->execute(['academia_id' => $academiaId, 'deporte_id' => (int) $deporteId]);
        }
    }

    public function cambiarEstado(int $id, bool $activo): bool
    {
        $stmt = $this->db->prepare('UPDATE academias SET activo = :activo WHERE id = :id');
        return $stmt->execute(['activo' => $activo ? 1 : 0, 'id' => $id]);
    }
}
