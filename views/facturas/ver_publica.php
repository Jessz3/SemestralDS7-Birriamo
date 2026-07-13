<div class="section-public" style="max-width:560px;">
    <h1>Tu inscripcion fue confirmada 🎉</h1>
    <p class="field-hint">Factura No. <?= htmlspecialchars($factura['numero_factura']) ?></p>

    <div class="card">
        <p><strong>Actividad:</strong> <?= htmlspecialchars($factura['actividad_nombre']) ?></p>
        <p><strong>Fecha de la actividad:</strong> <?= htmlspecialchars(substr($factura['actividad_fecha'], 0, 16)) ?></p>

        <table class="data-table" style="margin-top:1rem;">
            <tbody>
                <tr><td>Subtotal</td><td>$<?= number_format((float) $factura['subtotal'], 2) ?></td></tr>
                <tr><td>ITBMS (<?= number_format((float) $factura['tasa_itbms'], 2) ?>%)</td><td>$<?= number_format((float) $factura['itbms'], 2) ?></td></tr>
                <tr><td><strong>Total</strong></td><td><strong>$<?= number_format((float) $factura['total'], 2) ?></strong></td></tr>
            </tbody>
        </table>

        <a class="btn btn-primary" style="margin-top:1rem;" href="/facturas/descargar?id=<?= (int) $factura['id'] ?>">Descargar factura en PDF</a>
        <a class="btn btn-outline" style="margin-top:1rem;" href="/">Volver al inicio</a>
    </div>
</div>
