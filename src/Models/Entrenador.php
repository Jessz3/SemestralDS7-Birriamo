<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Entrenador extends Model
{
    public function todos(): array
    {
        $sql = "SELECT e.*, a.nombre AS academia_nombre
                FROM entrenadores e
                LEFT JOIN academias a ON a.id = e.academia_id
                ORDER BY e.nombre_completo ASC";
        return $this->db->query($sql)->fetchAll();
    }

    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM entrenadores WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function deportesDeEntrenador(int $entrenadorId): array
    {
        $stmt = $this->db->prepare(
            'SELECT d.* FROM entrenador_deportes ed
             JOIN deportes d ON d.id = ed.deporte_id
             WHERE ed.entrenador_id = :entrenador_id'
        );
        $stmt->execute(['entrenador_id' => $entrenadorId]);
        return $stmt->fetchAll();
    }

    public function crear(array $datos): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO entrenadores
                (organizador_id, academia_id, nombre_completo, cedula, correo, telefono,
                 certificaciones, anios_experiencia)
             VALUES
                (:organizador_id, :academia_id, :nombre_completo, :cedula, :correo, :telefono,
                 :certificaciones, :anios_experiencia)'
        );
        $stmt->execute([
            'organizador_id' => $datos['organizador_id'] ?: null,
            'academia_id' => $datos['academia_id'] ?: null,
            'nombre_completo' => $datos['nombre_completo'],
            'cedula' => $datos['cedula'] ?: null,
            'correo' => $datos['correo'] ?: null,
            'telefono' => $datos['telefono'] ?? null,
            'certificaciones' => $datos['certificaciones'] ?? null,
            'anios_experiencia' => $datos['anios_experiencia'] ?: null,
        ]);
        $id = (int) $this->db->lastInsertId();
        $this->sincronizarDeportes($id, $datos['deportes'] ?? []);
        return $id;
    }

    private function sincronizarDeportes(int $entrenadorId, array $deporteIds): void
    {
        $stmt = $this->db->prepare('DELETE FROM entrenador_deportes WHERE entrenador_id = :entrenador_id');
        $stmt->execute(['entrenador_id' => $entrenadorId]);

        if (empty($deporteIds)) {
            return;
        }

        $insertar = $this->db->prepare(
            'INSERT INTO entrenador_deportes (entrenador_id, deporte_id) VALUES (:entrenador_id, :deporte_id)'
        );
        foreach ($deporteIds as $deporteId) {
            $insertar->execute(['entrenador_id' => $entrenadorId, 'deporte_id' => (int) $deporteId]);
        }
    }
}
