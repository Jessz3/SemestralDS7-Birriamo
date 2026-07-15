<div class="container" style="max-width:640px;">
    <div class="page-head"><div><div class="eyebrow">Facturacion</div><h1>Factura <?= htmlspecialchars($factura['numero_factura']) ?></h1></div></div>

    <div class="card">
        <div class="card-header-row">
            <h2>Resumen</h2>
            <?php if ($integra): ?>
                <span class="badge badge-success">Firma digital valida</span>
            <?php else: ?>
                <span class="badge badge-danger">⚠ Firma invalida - posible alteracion</span>
            <?php endif; ?>
        </div>
        <p><strong>Cliente:</strong> <?= htmlspecialchars($factura['nombre_cliente']) ?></p>
        <p><strong>Actividad:</strong> <?= htmlspecialchars($factura['actividad_nombre']) ?></p>
        <p><strong>Fecha de emision:</strong> <?= htmlspecialchars($factura['fecha_venta']) ?></p>

        <table class="data-table" style="margin-top:1rem;">
            <thead><tr><th>Descripcion</th><th>Cant.</th><th>Precio</th><th>Subtotal</th></tr></thead>
            <tbody>
                <?php foreach ($factura['detalles'] as $d): ?>
                    <tr>
                        <td><?= htmlspecialchars($d['descripcion']) ?></td>
                        <td><?= number_format((float) $d['cantidad'], 2) ?></td>
                        <td>$<?= number_format((float) $d['precio_unitario'], 2) ?></td>
                        <td>$<?= number_format((float) $d['subtotal_linea'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr><td colspan="3" style="text-align:right;">Subtotal</td><td>$<?= number_format((float) $factura['subtotal'], 2) ?></td></tr>
                <tr><td colspan="3" style="text-align:right;">ITBMS (<?= number_format((float) $factura['tasa_itbms'], 2) ?>%)</td><td>$<?= number_format((float) $factura['itbms'], 2) ?></td></tr>
                <tr><td colspan="3" style="text-align:right;"><strong>Total</strong></td><td><strong>$<?= number_format((float) $factura['total'], 2) ?></strong></td></tr>
            </tbody>
        </table>

        <p style="margin-top:1rem;"><strong>Firma digital (<?= htmlspecialchars($factura['algoritmo_firma']) ?>):</strong></p>
        <div class="signature-box"><?= htmlspecialchars($factura['firma_digital']) ?></div>

        <?php if (!empty($factura['pdf_hash_sha256'])): ?>
            <p style="margin-top:.8rem;"><strong>Hash SHA-256 del PDF generado:</strong></p>
            <div class="signature-box"><?= htmlspecialchars($factura['pdf_hash_sha256']) ?></div>
        <?php endif; ?>

        <a class="btn btn-primary" style="margin-top:1rem;" href="<?= BASE_URL ?>/facturas/descargar?id=<?= (int) $factura['id'] ?>">Descargar PDF firmado</a>
        <a class="btn btn-outline" style="margin-top:1rem;" href="<?= BASE_URL ?>/facturas">← Volver</a>
    </div>
</div>
