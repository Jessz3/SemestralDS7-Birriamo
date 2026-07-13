<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class InscripcionEquipo extends Model
{
    public function porActividad(int $actividadId): array
    {
        $sql = "SELECT ie.*, eq.nombre AS equipo_nombre, eq.avatar
                FROM inscripciones_equipos ie
                JOIN equipos eq ON eq.id = ie.equipo_id
                WHERE ie.actividad_id = :actividad_id
                ORDER BY ie.fecha_inscripcion DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['actividad_id' => $actividadId]);
        return $stmt->fetchAll();
    }

    public function buscarPorId(int $id): ?array
    {
        $sql = "SELECT ie.*, eq.nombre AS equipo_nombre, eq.participante_id,
                    a.nombre AS actividad_nombre, a.costo_inscripcion, a.cupos_disponibles
                FROM inscripciones_equipos ie
                JOIN equipos eq ON eq.id = ie.equipo_id
                JOIN actividades a ON a.id = ie.actividad_id
                WHERE ie.id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function yaInscrito(int $actividadId, int $equipoId): bool
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM inscripciones_equipos WHERE actividad_id = :actividad_id AND equipo_id = :equipo_id'
        );
        $stmt->execute(['actividad_id' => $actividadId, 'equipo_id' => $equipoId]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function crear(int $actividadId, int $equipoId): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO inscripciones_equipos (actividad_id, equipo_id, estado, reglas_aceptadas)
             VALUES (:actividad_id, :equipo_id, 'PENDIENTE_APROBACION', 1)"
        );
        $stmt->execute(['actividad_id' => $actividadId, 'equipo_id' => $equipoId]);
        return (int) $this->db->lastInsertId();
    }

    public function aprobar(int $id, int $usuarioId): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE inscripciones_equipos SET estado = 'APROBADA', aprobado_por = :usuario_id,
             fecha_aprobacion = NOW() WHERE id = :id"
        );
        return $stmt->execute(['usuario_id' => $usuarioId, 'id' => $id]);
    }

    public function rechazar(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE inscripciones_equipos SET estado = 'RECHAZADA' WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function cancelarPorActividad(int $actividadId): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE inscripciones_equipos SET estado = 'CANCELADA'
             WHERE actividad_id = :actividad_id AND estado NOT IN ('CANCELADA', 'RECHAZADA')"
        );
        return $stmt->execute(['actividad_id' => $actividadId]);
    }
}
