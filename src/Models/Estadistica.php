<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Estadistica extends Model
{
    public function resumenGeneral(?int $organizadorId = null): array
    {
        if ($organizadorId === null) {
            $totalActividades = (int) $this->db->query('SELECT COUNT(*) FROM actividades')->fetchColumn();
            $actividadesFinalizadas = (int) $this->db->query("SELECT COUNT(*) FROM actividades WHERE estado = 'FINALIZADA'")->fetchColumn();
            $totalIncidentes = (int) $this->db->query('SELECT COUNT(*) FROM incidentes_deportivos')->fetchColumn();
            $totalRecaudado = (float) $this->db->query("SELECT COALESCE(SUM(total),0) FROM facturas WHERE estado = 'EMITIDA'")->fetchColumn();
            $totalEquipos = (int) $this->db->query('SELECT COUNT(*) FROM equipos')->fetchColumn();
            $totalInscripciones = (int) $this->db->query('SELECT COUNT(*) FROM inscripciones_equipos')->fetchColumn()
                + (int) $this->db->query('SELECT COUNT(*) FROM inscripciones_individuales')->fetchColumn();

            return compact(
                'totalActividades',
                'actividadesFinalizadas',
                'totalIncidentes',
                'totalRecaudado',
                'totalEquipos',
                'totalInscripciones'
            );
        }

        $params = ['organizador_id' => $organizadorId];

        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM actividades WHERE organizador_id = :organizador_id'
        );
        $stmt->execute($params);
        $totalActividades = (int) $stmt->fetchColumn();

        $stmt = $this->db->prepare(
            "SELECT COUNT(*)
             FROM actividades
             WHERE organizador_id = :organizador_id AND estado = 'FINALIZADA'"
        );
        $stmt->execute($params);
        $actividadesFinalizadas = (int) $stmt->fetchColumn();

        $stmt = $this->db->prepare(
            'SELECT COUNT(*)
             FROM incidentes_deportivos i
             INNER JOIN actividades a ON a.id = i.actividad_id
             WHERE a.organizador_id = :organizador_id'
        );
        $stmt->execute($params);
        $totalIncidentes = (int) $stmt->fetchColumn();

        $stmt = $this->db->prepare(
            "SELECT COALESCE(SUM(f.total),0)
             FROM facturas f
             INNER JOIN actividades a ON a.id = f.actividad_id
             WHERE f.estado = 'EMITIDA' AND a.organizador_id = :organizador_id"
        );
        $stmt->execute($params);
        $totalRecaudado = (float) $stmt->fetchColumn();

        $stmt = $this->db->prepare(
            'SELECT COUNT(DISTINCT ie.equipo_id)
             FROM inscripciones_equipos ie
             INNER JOIN actividades a ON a.id = ie.actividad_id
             WHERE a.organizador_id = :organizador_id'
        );
        $stmt->execute($params);
        $totalEquipos = (int) $stmt->fetchColumn();

        $stmt = $this->db->prepare(
            'SELECT COUNT(*)
             FROM inscripciones_equipos ie
             INNER JOIN actividades a ON a.id = ie.actividad_id
             WHERE a.organizador_id = :organizador_id'
        );
        $stmt->execute($params);
        $inscripcionesEquipos = (int) $stmt->fetchColumn();

        $stmt = $this->db->prepare(
            'SELECT COUNT(*)
             FROM inscripciones_individuales ii
             INNER JOIN actividades a ON a.id = ii.actividad_id
             WHERE a.organizador_id = :organizador_id'
        );
        $stmt->execute($params);
        $inscripcionesIndividuales = (int) $stmt->fetchColumn();

        $totalInscripciones = $inscripcionesEquipos + $inscripcionesIndividuales;

        return compact(
            'totalActividades',
            'actividadesFinalizadas',
            'totalIncidentes',
            'totalRecaudado',
            'totalEquipos',
            'totalInscripciones'
        );
    }

    public function actividadesPorDeporte(?int $organizadorId = null): array
    {
        if ($organizadorId === null) {
            $sql = 'SELECT d.nombre AS deporte, COUNT(a.id) AS total
                    FROM deportes d
                    LEFT JOIN actividades a ON a.deporte_id = d.id
                    GROUP BY d.id, d.nombre
                    ORDER BY total DESC';
            return $this->db->query($sql)->fetchAll();
        }

        $stmt = $this->db->prepare(
            'SELECT d.nombre AS deporte, COUNT(a.id) AS total
             FROM deportes d
             LEFT JOIN actividades a ON a.deporte_id = d.id AND a.organizador_id = :organizador_id
             GROUP BY d.id, d.nombre
             ORDER BY total DESC'
        );
        $stmt->execute(['organizador_id' => $organizadorId]);
        return $stmt->fetchAll();
    }

    /** Usa la vista vw_desempeno_arbitros o calcula el ranking por organizador. */
    public function rankingArbitros(?int $organizadorId = null): array
    {
        if ($organizadorId === null) {
            $sql = 'SELECT nombre_completo, promedio_general, total_evaluaciones
                    FROM vw_desempeno_arbitros
                    WHERE total_evaluaciones > 0
                    ORDER BY promedio_general DESC
                    LIMIT 10';
            return $this->db->query($sql)->fetchAll();
        }

        $sql = 'SELECT nombre_completo, promedio_general, total_evaluaciones
                FROM (
                    SELECT ar.nombre_completo AS nombre_completo,
                           ROUND(AVG(ea.puntuacion), 2) AS promedio_general,
                           COUNT(ea.id) AS total_evaluaciones
                    FROM evaluaciones_arbitros ea
                    INNER JOIN arbitros ar ON ar.id = ea.arbitro_id
                    WHERE ea.organizador_id = :organizador_id
                    GROUP BY ar.id, ar.nombre_completo
                ) evaluaciones
                WHERE total_evaluaciones > 0
                ORDER BY promedio_general DESC
                LIMIT 10';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['organizador_id' => $organizadorId]);
        return $stmt->fetchAll();
    }

    public function incidentesPorTipo(?int $organizadorId = null): array
    {
        if ($organizadorId === null) {
            $sql = 'SELECT tipo, COUNT(*) AS total FROM incidentes_deportivos GROUP BY tipo ORDER BY total DESC';
            return $this->db->query($sql)->fetchAll();
        }

        $stmt = $this->db->prepare(
            'SELECT i.tipo, COUNT(*) AS total
             FROM incidentes_deportivos i
             INNER JOIN actividades a ON a.id = i.actividad_id
             WHERE a.organizador_id = :organizador_id
             GROUP BY i.tipo
             ORDER BY total DESC'
        );
        $stmt->execute(['organizador_id' => $organizadorId]);
        return $stmt->fetchAll();
    }

    public function recaudacionPorMes(?int $organizadorId = null): array
    {
        if ($organizadorId === null) {
            $sql = "SELECT DATE_FORMAT(fecha_venta, '%Y-%m') AS mes, SUM(total) AS total
                    FROM facturas
                    WHERE estado = 'EMITIDA'
                    GROUP BY mes
                    ORDER BY mes ASC";
            return $this->db->query($sql)->fetchAll();
        }

        $stmt = $this->db->prepare(
            "SELECT DATE_FORMAT(f.fecha_venta, '%Y-%m') AS mes, SUM(f.total) AS total
             FROM facturas f
             INNER JOIN actividades a ON a.id = f.actividad_id
             WHERE f.estado = 'EMITIDA' AND a.organizador_id = :organizador_id
             GROUP BY mes
             ORDER BY mes ASC"
        );
        $stmt->execute(['organizador_id' => $organizadorId]);
        return $stmt->fetchAll();
    }
}
