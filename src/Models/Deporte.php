<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Deporte extends Model
{
    public function todos(bool $soloActivos = false): array
    {
        $sql = 'SELECT * FROM deportes';
        if ($soloActivos) {
            $sql .= ' WHERE activo = 1';
        }
        $sql .= ' ORDER BY nombre ASC';
        return $this->db->query($sql)->fetchAll();
    }

    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM deportes WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function nombreExiste(string $nombre, ?int $ignorarId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM deportes WHERE nombre = :nombre';
        $params = ['nombre' => $nombre];
        if ($ignorarId !== null) {
            $sql .= ' AND id != :id';
            $params['id'] = $ignorarId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function crear(array $datos): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO deportes (nombre, descripcion, es_equipo, minimo_jugadores, maximo_jugadores, activo)
             VALUES (:nombre, :descripcion, :es_equipo, :minimo_jugadores, :maximo_jugadores, :activo)'
        );
        $stmt->execute([
            'nombre' => $datos['nombre'],
            'descripcion' => $datos['descripcion'] ?? null,
            'es_equipo' => $datos['es_equipo'] ?? 0,
            'minimo_jugadores' => $datos['minimo_jugadores'] ?: null,
            'maximo_jugadores' => $datos['maximo_jugadores'] ?: null,
            'activo' => $datos['activo'] ?? 1,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function actualizar(int $id, array $datos): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE deportes SET nombre = :nombre, descripcion = :descripcion, es_equipo = :es_equipo,
             minimo_jugadores = :minimo_jugadores, maximo_jugadores = :maximo_jugadores WHERE id = :id'
        );
        return $stmt->execute([
            'nombre' => $datos['nombre'],
            'descripcion' => $datos['descripcion'] ?? null,
            'es_equipo' => $datos['es_equipo'] ?? 0,
            'minimo_jugadores' => $datos['minimo_jugadores'] ?: null,
            'maximo_jugadores' => $datos['maximo_jugadores'] ?: null,
            'id' => $id,
        ]);
    }

    public function cambiarEstado(int $id, bool $activo): bool
    {
        $stmt = $this->db->prepare('UPDATE deportes SET activo = :activo WHERE id = :id');
        return $stmt->execute(['activo' => $activo ? 1 : 0, 'id' => $id]);
    }
}
