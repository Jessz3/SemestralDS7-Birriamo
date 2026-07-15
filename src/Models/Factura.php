<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Security\FirmaDigitalHmacService;

final class Factura extends Model
{
    private FirmaDigitalHmacService $firma;

    public function __construct()
    {
        parent::__construct();
        $this->firma = new FirmaDigitalHmacService();
    }

    public function todas(): array
    {
        $sql = 'SELECT f.*, a.nombre AS actividad_nombre
                FROM facturas f
                JOIN actividades a ON a.id = f.actividad_id
                ORDER BY f.fecha_venta DESC';
        return $this->db->query($sql)->fetchAll();
    }

    public function porParticipante(int $participanteId): array
    {
        $stmt = $this->db->prepare(
            'SELECT f.*, a.nombre AS actividad_nombre
             FROM facturas f
             JOIN actividades a ON a.id = f.actividad_id
             WHERE f.participante_id = :participante_id
             ORDER BY f.fecha_venta DESC'
        );
        $stmt->execute(['participante_id' => $participanteId]);
        return $stmt->fetchAll();
    }

    public function buscarPorId(int $id): ?array
    {
        $sql = 'SELECT f.*, a.nombre AS actividad_nombre, a.fecha_inicio AS actividad_fecha, a.tipo AS actividad_tipo
                FROM facturas f
                JOIN actividades a ON a.id = f.actividad_id
                WHERE f.id = :id LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $factura = $stmt->fetch();

        if ($factura) {
            $factura['detalles'] = $this->detalles($id);
        }

        return $factura ?: null;
    }

    public function detalles(int $facturaId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM factura_detalles WHERE factura_id = :factura_id');
        $stmt->execute(['factura_id' => $facturaId]);
        return $stmt->fetchAll();
    }

    private function tasaItbms(): float
    {
        $stmt = $this->db->query("SELECT valor FROM configuracion_sistema WHERE clave = 'ITBMS_PORCENTAJE'");
        $valor = $stmt->fetchColumn();
        return $valor !== false ? (float) $valor : 7.00;
    }

    private function generarNumeroFactura(): string
    {
        $stmt = $this->db->query("SELECT valor FROM configuracion_sistema WHERE clave = 'PREFIJO_FACTURA'");
        $prefijo = $stmt->fetchColumn() ?: 'FAC';
        return $prefijo . '-' . date('Ymd') . '-' . str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);
    }

    /**
     * Emite una factura ligada a un pago ya aprobado. Calcula el ITBMS
     * segun la tasa configurable, firma digitalmente el contenido
     * (HMAC-SHA256, etiquetado como algoritmo_firma) y crea el detalle
     * de linea correspondiente.
     */
    public function emitir(array $datos, int $emitidoPor): int
    {
        $subtotal = round((float) $datos['subtotal'], 2);
        $tasa = $this->tasaItbms();
        $itbms = round($subtotal * ($tasa / 100), 2);
        $total = round($subtotal + $itbms, 2);
        $numero = $this->generarNumeroFactura();

        $cadena = FirmaDigitalHmacService::cadenaCanonica([
            $numero, $datos['pago_id'], $datos['actividad_id'],
            number_format($subtotal, 2, '.', ''), number_format($itbms, 2, '.', ''), number_format($total, 2, '.', ''),
        ]);
        $firmaDigital = $this->firma->proteger($cadena);

        $stmt = $this->db->prepare(
            'INSERT INTO facturas
                (numero_factura, pago_id, participante_id, actividad_id, equipo_id,
                 nombre_cliente, identificacion_cliente, correo_cliente,
                 subtotal, tasa_itbms, itbms, total, firma_digital, algoritmo_firma, fecha_firma)
             VALUES
                (:numero_factura, :pago_id, :participante_id, :actividad_id, :equipo_id,
                 :nombre_cliente, :identificacion_cliente, :correo_cliente,
                 :subtotal, :tasa_itbms, :itbms, :total, :firma_digital, :algoritmo_firma, NOW())'
        );

        $stmt->execute([
            'numero_factura' => $numero,
            'pago_id' => $datos['pago_id'],
            'participante_id' => $datos['participante_id'],
            'actividad_id' => $datos['actividad_id'],
            'equipo_id' => $datos['equipo_id'] ?? null,
            'nombre_cliente' => $datos['nombre_cliente'],
            'identificacion_cliente' => $datos['identificacion_cliente'] ?? null,
            'correo_cliente' => $datos['correo_cliente'] ?? null,
            'subtotal' => $subtotal,
            'tasa_itbms' => $tasa,
            'itbms' => $itbms,
            'total' => $total,
            'firma_digital' => $firmaDigital,
            'algoritmo_firma' => 'HMAC-SHA256',
        ]);

        $facturaId = (int) $this->db->lastInsertId();

        $this->agregarDetalle($facturaId, $datos['concepto'] ?? 'Inscripción a actividad', 1, $subtotal);

        return $facturaId;
    }

    public function agregarDetalle(int $facturaId, string $descripcion, float $cantidad, float $precioUnitario): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO factura_detalles (factura_id, descripcion, cantidad, precio_unitario, subtotal_linea)
             VALUES (:factura_id, :descripcion, :cantidad, :precio_unitario, :subtotal_linea)'
        );
        $stmt->execute([
            'factura_id' => $facturaId,
            'descripcion' => $descripcion,
            'cantidad' => $cantidad,
            'precio_unitario' => $precioUnitario,
            'subtotal_linea' => round($cantidad * $precioUnitario, 2),
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function actualizarRutaPdf(int $id, string $ruta, string $hashSha256): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE facturas SET ruta_pdf = :ruta, pdf_hash_sha256 = :hash WHERE id = :id'
        );
        return $stmt->execute(['ruta' => $ruta, 'hash' => $hashSha256, 'id' => $id]);
    }

    public function verificarIntegridad(array $factura): bool
    {
        $cadena = FirmaDigitalHmacService::cadenaCanonica([
            $factura['numero_factura'], $factura['pago_id'], $factura['actividad_id'],
            number_format((float) $factura['subtotal'], 2, '.', ''),
            number_format((float) $factura['itbms'], 2, '.', ''),
            number_format((float) $factura['total'], 2, '.', ''),
        ]);
        return $this->firma->verificar($cadena, $factura['firma_digital']);
    }

    public function anular(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE facturas SET estado = 'ANULADA' WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
