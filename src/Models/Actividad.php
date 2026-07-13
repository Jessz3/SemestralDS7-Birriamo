<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Actividad extends Model
{
    public const TIPOS = ['BIRRIA', 'ENTRENAMIENTO', 'TORNEO', 'EVENTO'];
    public const MODALIDADES = ['INDIVIDUAL', 'EQUIPO', 'MIXTA'];
    public const ESTADOS = ['BORRADOR', 'PUBLICADA', 'CERRADA', 'FINALIZADA', 'CANCELADA', 'TRASLADADA'];

    private const SELECT_BASE = "SELECT act.*, d.nombre AS deporte_nombre, i.nombre AS instalacion_nombre,
            i.direccion AS instalacion_direccion, e.nombre_completo AS entrenador_nombre,
            o.id AS organizador_id_real, CONCAT(u.nombre, ' ', u.apellido) AS organizador_nombre
        FROM actividades act
        JOIN deportes d ON d.id = act.deporte_id
        JOIN instalaciones i ON i.id = act.instalacion_id
        JOIN organizadores o ON o.id = act.organizador_id
        JOIN usuarios u ON u.id = o.usuario_id
        LEFT JOIN entrenadores e ON e.id = act.entrenador_id";

    public function todos(): array
    {
        $sql = self::SELECT_BASE . ' ORDER BY act.fecha_inicio DESC';
        return $this->db->query($sql)->fetchAll();
    }

    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->db->prepare(self::SELECT_BASE . ' WHERE act.id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function buscarPorToken(string $token): ?array
    {
        $stmt = $this->db->prepare(self::SELECT_BASE . ' WHERE act.token_publico = :token LIMIT 1');
        $stmt->execute(['token' => $token]);
        return $stmt->fetch() ?: null;
    }

    /** Usa la vista vw_actividades_publicas (solo actividades PUBLICADA y futuras). */
    public function disponiblesPublico(): array
    {
        return $this->db->query('SELECT * FROM vw_actividades_publicas ORDER BY fecha_inicio ASC')->fetchAll();
    }

    private function generarTokenPublico(): string
    {
        return bin2hex(random_bytes(32)); // 64 caracteres hexadecimales
    }

    public function crear(array $datos, int $organizadorId): int
    {
        $token = $this->generarTokenPublico();

        $stmt = $this->db->prepare(
            'INSERT INTO actividades
                (organizador_id, deporte_id, instalacion_id, entrenador_id, tipo, modalidad,
                 nombre, descripcion, reglas, fecha_inicio, fecha_fin, fecha_cierre_inscripcion,
                 edad_minima, edad_maxima, cupos_disponibles, capacidad_invitados, requiere_pago,
                 costo_inscripcion, costo_instalacion, token_publico, codigo_qr, estado)
             VALUES
                (:organizador_id, :deporte_id, :instalacion_id, :entrenador_id, :tipo, :modalidad,
                 :nombre, :descripcion, :reglas, :fecha_inicio, :fecha_fin, :fecha_cierre_inscripcion,
                 :edad_minima, :edad_maxima, :cupos_disponibles, :capacidad_invitados, :requiere_pago,
                 :costo_inscripcion, :costo_instalacion, :token_publico, :codigo_qr, :estado)'
        );

        $stmt->execute([
            'organizador_id' => $organizadorId,
            'deporte_id' => $datos['deporte_id'],
            'instalacion_id' => $datos['instalacion_id'],
            'entrenador_id' => $datos['entrenador_id'] ?: null,
            'tipo' => $datos['tipo'],
            'modalidad' => $datos['modalidad'],
            'nombre' => $datos['nombre'],
            'descripcion' => $datos['descripcion'],
            'reglas' => $datos['reglas'] ?? null,
            'fecha_inicio' => $datos['fecha_inicio'],
            'fecha_fin' => $datos['fecha_fin'],
            'fecha_cierre_inscripcion' => $datos['fecha_cierre_inscripcion'] ?: null,
            'edad_minima' => $datos['edad_minima'] ?: null,
            'edad_maxima' => $datos['edad_maxima'] ?: null,
            'cupos_disponibles' => $datos['cupos_disponibles'],
            'capacidad_invitados' => $datos['capacidad_invitados'] ?? 0,
            'requiere_pago' => $datos['requiere_pago'] ?? 0,
            'costo_inscripcion' => $datos['costo_inscripcion'] ?? 0,
            'costo_instalacion' => $datos['costo_instalacion'] ?? 0,
            'token_publico' => $token,
            'codigo_qr' => BASE_URL . '/evento/' . $token,
            'estado' => $datos['estado'] ?? 'BORRADOR',
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function actualizar(int $id, array $datos): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE actividades SET
                deporte_id = :deporte_id, instalacion_id = :instalacion_id, entrenador_id = :entrenador_id,
                tipo = :tipo, modalidad = :modalidad, nombre = :nombre, descripcion = :descripcion,
                reglas = :reglas, fecha_inicio = :fecha_inicio, fecha_fin = :fecha_fin,
                fecha_cierre_inscripcion = :fecha_cierre_inscripcion, edad_minima = :edad_minima,
                edad_maxima = :edad_maxima, cupos_disponibles = :cupos_disponibles,
                capacidad_invitados = :capacidad_invitados, requiere_pago = :requiere_pago,
                costo_inscripcion = :costo_inscripcion, costo_instalacion = :costo_instalacion
             WHERE id = :id'
        );

        return $stmt->execute([
            'deporte_id' => $datos['deporte_id'],
            'instalacion_id' => $datos['instalacion_id'],
            'entrenador_id' => $datos['entrenador_id'] ?: null,
            'tipo' => $datos['tipo'],
            'modalidad' => $datos['modalidad'],
            'nombre' => $datos['nombre'],
            'descripcion' => $datos['descripcion'],
            'reglas' => $datos['reglas'] ?? null,
            'fecha_inicio' => $datos['fecha_inicio'],
            'fecha_fin' => $datos['fecha_fin'],
            'fecha_cierre_inscripcion' => $datos['fecha_cierre_inscripcion'] ?: null,
            'edad_minima' => $datos['edad_minima'] ?: null,
            'edad_maxima' => $datos['edad_maxima'] ?: null,
            'cupos_disponibles' => $datos['cupos_disponibles'],
            'capacidad_invitados' => $datos['capacidad_invitados'] ?? 0,
            'requiere_pago' => $datos['requiere_pago'] ?? 0,
            'costo_inscripcion' => $datos['costo_inscripcion'] ?? 0,
            'costo_instalacion' => $datos['costo_instalacion'] ?? 0,
            'id' => $id,
        ]);
    }

    public function publicar(int $id): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE actividades SET estado = 'PUBLICADA', fecha_publicacion = NOW() WHERE id = :id AND estado = 'BORRADOR'"
        );
        return $stmt->execute(['id' => $id]);
    }

    public function cerrarInscripciones(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE actividades SET estado = 'CERRADA' WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function finalizar(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE actividades SET estado = 'FINALIZADA' WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function cancelar(int $id, string $motivo): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE actividades SET estado = 'CANCELADA', motivo_cancelacion = :motivo WHERE id = :id"
        );
        return $stmt->execute(['motivo' => $motivo, 'id' => $id]);
    }

    public function cuposOcupados(int $actividadId): int
    {
        $sql = "SELECT
            (SELECT COUNT(*) FROM inscripciones_equipos WHERE actividad_id = :id1 AND estado IN ('APROBADA','FINALIZADA'))
            +
            (SELECT COUNT(*) FROM inscripciones_individuales WHERE actividad_id = :id2 AND estado IN ('APROBADA','FINALIZADA'))
            AS total";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id1' => $actividadId, 'id2' => $actividadId]);
        return (int) $stmt->fetchColumn();
    }
}
