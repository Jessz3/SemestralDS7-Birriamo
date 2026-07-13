<div class="container">
    <div class="page-head"><div><div class="eyebrow">Administracion</div><h1>Mensajes de Contacto</h1></div></div>

    <div class="card">
        <table class="data-table">
            <thead><tr><th>Nombre</th><th>Correo</th><th>Asunto</th><th>Mensaje</th><th>Estado</th><th>Fecha</th><th></th></tr></thead>
            <tbody>
                <?php foreach ($mensajes as $m): ?>
                    <tr>
                        <td><?= htmlspecialchars($m['nombre']) ?></td>
                        <td><?= htmlspecialchars($m['correo']) ?></td>
                        <td><?= htmlspecialchars($m['asunto']) ?></td>
                        <td style="max-width:280px;"><?= htmlspecialchars($m['mensaje']) ?></td>
                        <td>
                            <?php if ($m['estado'] === 'NUEVO'): ?><span class="badge badge-warning">Nuevo</span>
                            <?php else: ?><span class="badge badge-neutral"><?= htmlspecialchars($m['estado']) ?></span><?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($m['fecha_envio']) ?></td>
                        <td>
                            <?php if ($m['estado'] === 'NUEVO'): ?>
                                <a class="btn btn-outline btn-sm" href="/configuracion/mensajes/leido?id=<?= (int) $m['id'] ?>">Marcar leido</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
