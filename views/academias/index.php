<div class="container">
    <div class="page-head">
        <div><div class="eyebrow">Catalogo</div><h1>Academias Deportivas</h1></div>
        <a class="btn btn-primary" href="/academias/crear">+ Nueva academia</a>
    </div>

    <?php require __DIR__ . '/../layout/_alerts.php'; ?>

    <div class="card">
        <table class="data-table">
            <thead><tr><th>Nombre</th><th>RUC</th><th>Telefono</th><th>Correo</th><th>Estado</th><th>Acciones</th></tr></thead>
            <tbody>
                <?php foreach ($academias as $a): ?>
                    <tr>
                        <td><?= htmlspecialchars($a['nombre']) ?></td>
                        <td><?= htmlspecialchars($a['ruc'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($a['telefono'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($a['correo'] ?? '-') ?></td>
                        <td>
                            <?php if ((int) $a['activo'] === 1): ?><span class="badge badge-success">Activa</span>
                            <?php else: ?><span class="badge badge-danger">Inactiva</span><?php endif; ?>
                        </td>
                        <td>
                            <a class="btn btn-outline btn-sm" href="/academias/editar?id=<?= (int) $a['id'] ?>">Editar</a>
                            <?php if ((int) $a['activo'] === 1): ?>
                                <a class="btn btn-danger btn-sm" href="/academias/deshabilitar?id=<?= (int) $a['id'] ?>&csrf_token=<?= urlencode($csrf) ?>">Deshabilitar</a>
                            <?php else: ?>
                                <a class="btn btn-primary btn-sm" href="/academias/habilitar?id=<?= (int) $a['id'] ?>&csrf_token=<?= urlencode($csrf) ?>">Habilitar</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
