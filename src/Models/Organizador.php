<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Organizador extends Model
{
    public const TIPOS = ['INDEPENDIENTE', 'ACADEMIA', 'ENTRENADOR', 'EMPRESA', 'COMITE', 'OTRO'];

    public function todos(): array
    {
        $sql = "SELECT o.*, CONCAT(u.nombre, ' ', u.apellido) AS nombre_completo, u.correo, u.telefono,
                    a.nombre AS academia_nombre
                FROM organizadores o
                JOIN usuarios u ON u.id = o.usuario_id
                LEFT JOIN academias a ON a.id = o.academia_id
                ORDER BY u.nombre ASC";
        return $this->db->query($sql)->fetchAll();
    }

    public function buscarPorId(int $id): ?array
    {
        $sql = "SELECT o.*, CONCAT(u.nombre, ' ', u.apellido) AS nombre_completo, u.correo, u.telefono
                FROM organizadores o
                JOIN usuarios u ON u.id = o.usuario_id
                WHERE o.id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function buscarPorUsuarioId(int $usuarioId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM organizadores WHERE usuario_id = :usuario_id LIMIT 1');
        $stmt->execute(['usuario_id' => $usuarioId]);
        return $stmt->fetch() ?: null;
    }

    public function crear(int $usuarioId, array $datos): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO organizadores (usuario_id, academia_id, tipo_organizador, nombre_comercial, descripcion)
             VALUES (:usuario_id, :academia_id, :tipo_organizador, :nombre_comercial, :descripcion)'
        );
        $stmt->execute([
            'usuario_id' => $usuarioId,
            'academia_id' => $datos['academia_id'] ?: null,
            'tipo_organizador' => $datos['tipo_organizador'] ?? 'INDEPENDIENTE',
            'nombre_comercial' => $datos['nombre_comercial'] ?? null,
            'descripcion' => $datos['descripcion'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function actualizar(int $id, array $datos): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE organizadores SET academia_id = :academia_id, tipo_organizador = :tipo_organizador,
             nombre_comercial = :nombre_comercial, descripcion = :descripcion WHERE id = :id'
        );
        return $stmt->execute([
            'academia_id' => $datos['academia_id'] ?: null,
            'tipo_organizador' => $datos['tipo_organizador'] ?? 'INDEPENDIENTE',
            'nombre_comercial' => $datos['nombre_comercial'] ?? null,
            'descripcion' => $datos['descripcion'] ?? null,
            'id' => $id,
        ]);
    }

    public function verificar(int $id, int $verificadoPor): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE organizadores SET verificado = 1, fecha_verificacion = NOW(), verificado_por = :verificado_por
             WHERE id = :id'
        );
        return $stmt->execute(['verificado_por' => $verificadoPor, 'id' => $id]);
    }

    public function cambiarEstado(int $id, bool $activo): bool
    {
        $stmt = $this->db->prepare('UPDATE organizadores SET activo = :activo WHERE id = :id');
        return $stmt->execute(['activo' => $activo ? 1 : 0, 'id' => $id]);
    }
}
