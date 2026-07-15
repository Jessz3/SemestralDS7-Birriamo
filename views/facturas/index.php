<div class="container">
    <div class="page-head"><div><div class="eyebrow">Facturacion</div><h1><?= !empty($esParticipante) ? 'Mis Facturas' : 'Facturas Emitidas' ?></h1></div></div>

    <div class="card">
        <table class="data-table">
            <thead><tr><th>No. Factura</th><th>Cliente</th><th>Actividad</th><th>Total</th><th>Estado</th><th>Fecha</th><th></th></tr></thead>
            <tbody>
                <?php if (empty($facturas)): ?>
                    <tr><td colspan="7" style="text-align:center;">No tienes facturas emitidas todavía.</td></tr>
                <?php endif; ?>
                <?php foreach ($facturas as $f): ?>
                    <tr>
                        <td><?= htmlspecialchars($f['numero_factura']) ?></td>
                        <td><?= htmlspecialchars($f['nombre_cliente']) ?></td>
                        <td><?= htmlspecialchars($f['actividad_nombre']) ?></td>
                        <td><strong>$<?= number_format((float) $f['total'], 2) ?></strong></td>
                        <td>
                            <?php if ($f['estado'] === 'EMITIDA'): ?><span class="badge badge-success">Emitida</span>
                            <?php else: ?><span class="badge badge-danger"><?= htmlspecialchars($f['estado']) ?></span><?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($f['fecha_venta']) ?></td>
                        <td>
                            <a class="btn btn-outline btn-sm" href="<?= BASE_URL ?>/facturas/ver?id=<?= (int) $f['id'] ?>">Ver</a>
                            <a class="btn btn-primary btn-sm" href="<?= BASE_URL ?>/facturas/descargar?id=<?= (int) $f['id'] ?>">Descargar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
