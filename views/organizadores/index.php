<div class="container">
    <div class="page-head">
        <div><div class="eyebrow">Catalogo</div><h1>Organizadores Deportivos</h1></div>
        <a class="btn btn-primary" href="/organizadores/crear">+ Nuevo organizador</a>
    </div>

    <?php require __DIR__ . '/../layout/_alerts.php'; ?>

    <div class="card">
        <table class="data-table">
            <thead><tr><th>Nombre</th><th>Correo</th><th>Tipo</th><th>Academia</th><th>Verificado</th><th>Estado</th><th>Acciones</th></tr></thead>
            <tbody>
                <?php foreach ($organizadores as $o): ?>
                    <tr>
                        <td><?= htmlspecialchars($o['nombre_completo']) ?><?= $o['nombre_comercial'] ? ' — ' . htmlspecialchars($o['nombre_comercial']) : '' ?></td>
                        <td><?= htmlspecialchars($o['correo']) ?></td>
                        <td><?= htmlspecialchars($o['tipo_organizador']) ?></td>
                        <td><?= htmlspecialchars($o['academia_nombre'] ?? '-') ?></td>
                        <td>
                            <?php if ((int) $o['verificado'] === 1): ?><span class="badge badge-success">Verificado</span>
                            <?php else: ?><a class="btn btn-outline btn-sm" href="/organizadores/verificar?id=<?= (int) $o['id'] ?>&csrf_token=<?= urlencode($csrf) ?>">Verificar</a><?php endif; ?>
                        </td>
                        <td>
                            <?php if ((int) $o['activo'] === 1): ?><span class="badge badge-success">Activo</span>
                            <?php else: ?><span class="badge badge-danger">Inactivo</span><?php endif; ?>
                        </td>
                        <td>
                            <a class="btn btn-outline btn-sm" href="/organizadores/editar?id=<?= (int) $o['id'] ?>">Editar</a>
                            <?php if ((int) $o['activo'] === 1): ?>
                                <a class="btn btn-danger btn-sm" href="/organizadores/deshabilitar?id=<?= (int) $o['id'] ?>&csrf_token=<?= urlencode($csrf) ?>">Deshabilitar</a>
                            <?php else: ?>
                                <a class="btn btn-primary btn-sm" href="/organizadores/habilitar?id=<?= (int) $o['id'] ?>&csrf_token=<?= urlencode($csrf) ?>">Habilitar</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
