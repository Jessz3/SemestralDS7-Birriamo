<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Equipo extends Model
{
    public function todos(): array
    {
        $sql = "SELECT eq.*, a.nombre AS academia_nombre, d.nombre AS deporte_nombre,
                    CONCAT(u.nombre, ' ', u.apellido) AS representante,
                    (SELECT COUNT(*) FROM jugadores j WHERE j.equipo_id = eq.id) AS total_jugadores
                FROM equipos eq
                JOIN participantes p ON p.id = eq.participante_id
                JOIN usuarios u ON u.id = p.usuario_id
                JOIN deportes d ON d.id = eq.deporte_id
                LEFT JOIN academias a ON a.id = eq.academia_id
                ORDER BY eq.nombre ASC";
        return $this->db->query($sql)->fetchAll();
    }

    public function porParticipante(int $participanteId): array
    {
        $sql = "SELECT eq.*, a.nombre AS academia_nombre, d.nombre AS deporte_nombre,
                    CONCAT(u.nombre, ' ', u.apellido) AS representante,
                    (SELECT COUNT(*) FROM jugadores j WHERE j.equipo_id = eq.id) AS total_jugadores
                FROM equipos eq
                JOIN participantes p ON p.id = eq.participante_id
                JOIN usuarios u ON u.id = p.usuario_id
                JOIN deportes d ON d.id = eq.deporte_id
                LEFT JOIN academias a ON a.id = eq.academia_id
                WHERE eq.participante_id = :participante_id
                ORDER BY eq.nombre ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['participante_id' => $participanteId]);
        return $stmt->fetchAll();
    }

    public function perteneceAUsuario(int $equipoId, int $usuarioId): bool
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM equipos eq
             JOIN participantes p ON p.id = eq.participante_id
             WHERE eq.id = :equipo_id AND p.usuario_id = :usuario_id'
        );
        $stmt->execute(['equipo_id' => $equipoId, 'usuario_id' => $usuarioId]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function buscarPorId(int $id): ?array
    {
        $sql = "SELECT eq.*, a.nombre AS academia_nombre, d.nombre AS deporte_nombre,
                    CONCAT(u.nombre, ' ', u.apellido) AS representante, u.correo AS correo_contacto
                FROM equipos eq
                JOIN participantes p ON p.id = eq.participante_id
                JOIN usuarios u ON u.id = p.usuario_id
                JOIN deportes d ON d.id = eq.deporte_id
                LEFT JOIN academias a ON a.id = eq.academia_id
                WHERE eq.id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function jugadoresDeEquipo(int $equipoId): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM jugadores WHERE equipo_id = :equipo_id ORDER BY capitan DESC, nombre_completo ASC'
        );
        $stmt->execute(['equipo_id' => $equipoId]);
        return $stmt->fetchAll();
    }

    public function crear(int $participanteId, array $datos): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO equipos (participante_id, academia_id, deporte_id, nombre, avatar, descripcion)
             VALUES (:participante_id, :academia_id, :deporte_id, :nombre, :avatar, :descripcion)'
        );
        $stmt->execute([
            'participante_id' => $participanteId,
            'academia_id' => $datos['academia_id'] ?: null,
            'deporte_id' => $datos['deporte_id'],
            'nombre' => $datos['nombre'],
            'avatar' => $datos['avatar'] ?? null,
            'descripcion' => $datos['descripcion'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function actualizar(int $id, array $datos): bool
    {
        $sql = 'UPDATE equipos SET nombre = :nombre, academia_id = :academia_id,
                 deporte_id = :deporte_id, descripcion = :descripcion';
        $params = [
            'nombre' => $datos['nombre'],
            'academia_id' => $datos['academia_id'] ?: null,
            'deporte_id' => $datos['deporte_id'],
            'descripcion' => $datos['descripcion'] ?? null,
            'id' => $id,
        ];

        if (!empty($datos['avatar'])) {
            $sql .= ', avatar = :avatar';
            $params['avatar'] = $datos['avatar'];
        }

        $sql .= ' WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function agregarJugador(int $equipoId, array $datos): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO jugadores (equipo_id, nombre_completo, edad, peso_kg, posicion, numero_camiseta, capitan)
             VALUES (:equipo_id, :nombre_completo, :edad, :peso_kg, :posicion, :numero_camiseta, :capitan)'
        );
        $stmt->execute([
            'equipo_id' => $equipoId,
            'nombre_completo' => $datos['nombre_completo'],
            'edad' => $datos['edad'],
            'peso_kg' => $datos['peso_kg'] ?: null,
            'posicion' => $datos['posicion'] ?? null,
            'numero_camiseta' => $datos['numero_camiseta'] ?: null,
            'capitan' => $datos['capitan'] ?? 0,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function eliminarJugador(int $jugadorId, ?int $equipoId = null): bool
    {
        $sql = 'DELETE FROM jugadores WHERE id = :id';
        $parametros = ['id' => $jugadorId];
        if ($equipoId !== null) {
            $sql .= ' AND equipo_id = :equipo_id';
            $parametros['equipo_id'] = $equipoId;
        }
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($parametros);
    }
}
