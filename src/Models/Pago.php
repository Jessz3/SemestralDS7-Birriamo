<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Pago extends Model
{
    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM pagos WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Registra un pago. En este sistema (sin pasarela de pago real) se
     * registra directamente como APROBADO al momento de la aprobacion
     * de la inscripcion (equivalente a un pago validado en sitio).
     */
    public function registrar(array $datos): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO pagos
                (inscripcion_individual_id, inscripcion_equipo_id, participante_id, metodo_pago,
                 referencia, monto, estado, fecha_validacion, validado_por)
             VALUES
                (:inscripcion_individual_id, :inscripcion_equipo_id, :participante_id, :metodo_pago,
                 :referencia, :monto, 'APROBADO', NOW(), :validado_por)"
        );

        $stmt->execute([
            'inscripcion_individual_id' => $datos['inscripcion_individual_id'] ?? null,
            'inscripcion_equipo_id' => $datos['inscripcion_equipo_id'] ?? null,
            'participante_id' => $datos['participante_id'],
            'metodo_pago' => $datos['metodo_pago'] ?? 'EFECTIVO',
            'referencia' => $datos['referencia'] ?? null,
            'monto' => $datos['monto'],
            'validado_por' => $datos['validado_por'] ?? null,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function anular(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE pagos SET estado = 'ANULADO' WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
