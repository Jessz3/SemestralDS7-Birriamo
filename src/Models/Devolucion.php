<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Devolucion extends Model
{
    public function porActividad(int $actividadId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM devoluciones WHERE actividad_id = :actividad_id');
        $stmt->execute(['actividad_id' => $actividadId]);
        return $stmt->fetchAll();
    }

    public function crear(int $pagoId, ?int $facturaId, int $actividadId, string $motivo, float $monto, ?int $solicitadaPor): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO devoluciones (pago_id, factura_id, actividad_id, motivo, monto, solicitada_por)
             VALUES (:pago_id, :factura_id, :actividad_id, :motivo, :monto, :solicitada_por)'
        );
        $stmt->execute([
            'pago_id' => $pagoId,
            'factura_id' => $facturaId,
            'actividad_id' => $actividadId,
            'motivo' => $motivo,
            'monto' => $monto,
            'solicitada_por' => $solicitadaPor,
        ]);
        return (int) $this->db->lastInsertId();
    }
}
