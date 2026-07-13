<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Estadistica extends Model
{
    public function resumenGeneral(): array
    {
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

    public function actividadesPorDeporte(): array
    {
        $sql = 'SELECT d.nombre AS deporte, COUNT(a.id) AS total
                FROM deportes d
                LEFT JOIN actividades a ON a.deporte_id = d.id
                GROUP BY d.id, d.nombre
                ORDER BY total DESC';
        return $this->db->query($sql)->fetchAll();
    }

    /** Usa la vista vw_desempeno_arbitros. */
    public function rankingArbitros(): array
    {
        $sql = 'SELECT nombre_completo, promedio_general, total_evaluaciones
                FROM vw_desempeno_arbitros
                WHERE total_evaluaciones > 0
                ORDER BY promedio_general DESC
                LIMIT 10';
        return $this->db->query($sql)->fetchAll();
    }

    public function incidentesPorTipo(): array
    {
        $sql = 'SELECT tipo, COUNT(*) AS total FROM incidentes_deportivos GROUP BY tipo ORDER BY total DESC';
        return $this->db->query($sql)->fetchAll();
    }

    public function recaudacionPorMes(): array
    {
        $sql = "SELECT DATE_FORMAT(fecha_venta, '%Y-%m') AS mes, SUM(total) AS total
                FROM facturas
                WHERE estado = 'EMITIDA'
                GROUP BY mes
                ORDER BY mes ASC";
        return $this->db->query($sql)->fetchAll();
    }
}
