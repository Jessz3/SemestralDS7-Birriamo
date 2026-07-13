<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class InscripcionIndividual extends Model
{
    public function porActividad(int $actividadId): array
    {
        $sql = "SELECT ii.*, CONCAT(u.nombre, ' ', u.apellido) AS nombre_completo, u.correo, u.telefono
                FROM inscripciones_individuales ii
                JOIN participantes p ON p.id = ii.participante_id
                JOIN usuarios u ON u.id = p.usuario_id
                WHERE ii.actividad_id = :actividad_id
                ORDER BY ii.fecha_inscripcion DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['actividad_id' => $actividadId]);
        return $stmt->fetchAll();
    }

    public function porParticipante(int $participanteId): array
    {
        $sql = "SELECT ii.*, a.nombre AS actividad_nombre, a.fecha_inicio, a.fecha_fin,
                       a.modalidad, d.nombre AS deporte_nombre, i.nombre AS instalacion_nombre
                FROM inscripciones_individuales ii
                JOIN actividades a ON a.id = ii.actividad_id
                JOIN deportes d ON d.id = a.deporte_id
                JOIN instalaciones i ON i.id = a.instalacion_id
                WHERE ii.participante_id = :participante_id
                ORDER BY a.fecha_inicio DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['participante_id' => $participanteId]);
        return $stmt->fetchAll();
    }

    public function buscarPorId(int $id): ?array
    {
        $sql = "SELECT ii.*, a.nombre AS actividad_nombre, a.costo_inscripcion,
                    CONCAT(u.nombre, ' ', u.apellido) AS nombre_completo, u.correo, p.id AS participante_id
                FROM inscripciones_individuales ii
                JOIN actividades a ON a.id = ii.actividad_id
                JOIN participantes p ON p.id = ii.participante_id
                JOIN usuarios u ON u.id = p.usuario_id
                WHERE ii.id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function yaInscrito(int $actividadId, int $participanteId): bool
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM inscripciones_individuales WHERE actividad_id = :actividad_id AND participante_id = :participante_id'
        );
        $stmt->execute(['actividad_id' => $actividadId, 'participante_id' => $participanteId]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function crear(int $actividadId, int $participanteId, string $estadoInicial = 'APROBADA'): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO inscripciones_individuales (actividad_id, participante_id, estado, reglas_aceptadas, fecha_aprobacion)
             VALUES (:actividad_id, :participante_id, :estado, 1, NOW())'
        );
        $stmt->execute([
            'actividad_id' => $actividadId,
            'participante_id' => $participanteId,
            'estado' => $estadoInicial,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function cancelarPorActividad(int $actividadId): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE inscripciones_individuales SET estado = 'CANCELADA'
             WHERE actividad_id = :actividad_id AND estado NOT IN ('CANCELADA', 'RECHAZADA')"
        );
        return $stmt->execute(['actividad_id' => $actividadId]);
    }
}
