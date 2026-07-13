<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Arbitro extends Model
{
    public function todos(): array
    {
        return $this->db->query('SELECT * FROM arbitros ORDER BY nombre_completo ASC')->fetchAll();
    }

    /**
     * Lista de arbitros con su desempeno promedio, calculado por la
     * vista vw_desempeno_arbitros definida en el script de base de datos.
     */
    public function todosConDesempeno(): array
    {
        $sql = 'SELECT ar.*, v.total_evaluaciones, v.promedio_general, v.promedio_puntualidad,
                    v.promedio_reglas, v.promedio_imparcialidad, v.promedio_manejo
                FROM arbitros ar
                LEFT JOIN vw_desempeno_arbitros v ON v.arbitro_id = ar.id
                ORDER BY ar.nombre_completo ASC';
        return $this->db->query($sql)->fetchAll();
    }

    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM arbitros WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function deportesDeArbitro(int $arbitroId): array
    {
        $stmt = $this->db->prepare(
            'SELECT d.* FROM arbitro_deportes ad
             JOIN deportes d ON d.id = ad.deporte_id
             WHERE ad.arbitro_id = :arbitro_id'
        );
        $stmt->execute(['arbitro_id' => $arbitroId]);
        return $stmt->fetchAll();
    }

    public function crear(array $datos): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO arbitros (nombre_completo, cedula, correo, telefono, licencia, experiencia)
             VALUES (:nombre_completo, :cedula, :correo, :telefono, :licencia, :experiencia)'
        );
        $stmt->execute([
            'nombre_completo' => $datos['nombre_completo'],
            'cedula' => $datos['cedula'] ?: null,
            'correo' => $datos['correo'] ?: null,
            'telefono' => $datos['telefono'] ?? null,
            'licencia' => $datos['licencia'] ?: null,
            'experiencia' => $datos['experiencia'] ?? null,
        ]);
        $id = (int) $this->db->lastInsertId();
        $this->sincronizarDeportes($id, $datos['deportes'] ?? []);
        return $id;
    }

    private function sincronizarDeportes(int $arbitroId, array $deporteIds): void
    {
        $stmt = $this->db->prepare('DELETE FROM arbitro_deportes WHERE arbitro_id = :arbitro_id');
        $stmt->execute(['arbitro_id' => $arbitroId]);

        if (empty($deporteIds)) {
            return;
        }

        $insertar = $this->db->prepare(
            'INSERT INTO arbitro_deportes (arbitro_id, deporte_id) VALUES (:arbitro_id, :deporte_id)'
        );
        foreach ($deporteIds as $deporteId) {
            $insertar->execute(['arbitro_id' => $arbitroId, 'deporte_id' => (int) $deporteId]);
        }
    }

    /** Registra la asignacion de un arbitro a una actividad (actividad_arbitros). */
    public function asignarAActividad(int $actividadId, int $arbitroId): void
    {
        $stmt = $this->db->prepare(
            'INSERT IGNORE INTO actividad_arbitros (actividad_id, arbitro_id) VALUES (:actividad_id, :arbitro_id)'
        );
        $stmt->execute(['actividad_id' => $actividadId, 'arbitro_id' => $arbitroId]);
    }

    public function arbitrosDeActividad(int $actividadId): array
    {
        $sql = 'SELECT aa.*, ar.nombre_completo
                FROM actividad_arbitros aa
                JOIN arbitros ar ON ar.id = aa.arbitro_id
                WHERE aa.actividad_id = :actividad_id';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['actividad_id' => $actividadId]);
        return $stmt->fetchAll();
    }

    public function registrarEvaluacion(
        int $actividadId,
        int $arbitroId,
        int $organizadorId,
        int $puntuacion,
        ?int $puntualidad,
        ?int $reglas,
        ?int $imparcialidad,
        ?int $manejo,
        ?string $comentario
    ): int {
        $stmt = $this->db->prepare(
            'INSERT INTO evaluaciones_arbitros
                (actividad_id, arbitro_id, organizador_id, puntuacion, puntualidad,
                 conocimiento_reglas, imparcialidad, manejo_actividad, comentario)
             VALUES
                (:actividad_id, :arbitro_id, :organizador_id, :puntuacion, :puntualidad,
                 :reglas, :imparcialidad, :manejo, :comentario)'
        );
        $stmt->execute([
            'actividad_id' => $actividadId,
            'arbitro_id' => $arbitroId,
            'organizador_id' => $organizadorId,
            'puntuacion' => $puntuacion,
            'puntualidad' => $puntualidad,
            'reglas' => $reglas,
            'imparcialidad' => $imparcialidad,
            'manejo' => $manejo,
            'comentario' => $comentario,
        ]);
        return (int) $this->db->lastInsertId();
    }
}
