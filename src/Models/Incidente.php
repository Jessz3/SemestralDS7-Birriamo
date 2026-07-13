<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Incidente extends Model
{
    public const TIPOS = ['LESION', 'CONDUCTA_ANTIDEPORTIVA', 'EXPULSION', 'DAÑO_INSTALACION', 'SUSPENSION', 'OTRO'];
    public const GRAVEDADES = ['LEVE', 'MODERADA', 'GRAVE', 'CRITICA'];

    public function porActividad(int $actividadId): array
    {
        $stmt = $this->db->prepare(
            'SELECT i.*, eq.nombre AS equipo_nombre
             FROM incidentes_deportivos i
             LEFT JOIN equipos eq ON eq.id = i.equipo_id
             WHERE i.actividad_id = :actividad_id ORDER BY i.fecha_incidente DESC'
        );
        $stmt->execute(['actividad_id' => $actividadId]);
        return $stmt->fetchAll();
    }

    public function crear(array $datos, int $usuarioId): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO incidentes_deportivos
                (actividad_id, reportado_por, equipo_id, tipo, gravedad, descripcion, fecha_incidente)
             VALUES
                (:actividad_id, :reportado_por, :equipo_id, :tipo, :gravedad, :descripcion, :fecha_incidente)'
        );
        $stmt->execute([
            'actividad_id' => $datos['actividad_id'],
            'reportado_por' => $usuarioId,
            'equipo_id' => $datos['equipo_id'] ?: null,
            'tipo' => $datos['tipo'],
            'gravedad' => $datos['gravedad'],
            'descripcion' => $datos['descripcion'],
            'fecha_incidente' => $datos['fecha_incidente'] ?: date('Y-m-d H:i:s'),
        ]);
        return (int) $this->db->lastInsertId();
    }
}
