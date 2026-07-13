<div class="container">
    <div class="page-head">
        <div><div class="eyebrow">Catalogo</div><h1>Instalaciones Deportivas</h1></div>
        <a class="btn btn-primary" href="/instalaciones/crear">+ Nueva instalacion</a>
    </div>

    <?php require __DIR__ . '/../layout/_alerts.php'; ?>

    <div class="card">
        <table class="data-table">
            <thead><tr><th>Nombre</th><th>Tipo</th><th>Ubicacion</th><th>Capacidad</th><th>Costo base</th><th>Estado</th><th>Acciones</th></tr></thead>
            <tbody>
                <?php foreach ($instalaciones as $i): ?>
                    <tr>
                        <td><?= htmlspecialchars($i['nombre']) ?></td>
                        <td><?= htmlspecialchars($i['tipo']) ?></td>
                        <td><?= htmlspecialchars($i['direccion']) ?>, <?= htmlspecialchars($i['provincia']) ?></td>
                        <td><?= (int) $i['capacidad_invitados'] ?></td>
                        <td>$<?= number_format((float) $i['costo_base'], 2) ?></td>
                        <td>
                            <?php if ((int) $i['activo'] === 1): ?>
                                <span class="badge badge-success">Activa</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Inactiva</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a class="btn btn-outline btn-sm" href="/instalaciones/editar?id=<?= (int) $i['id'] ?>">Editar</a>
                            <?php if ((int) $i['activo'] === 1): ?>
                                <a class="btn btn-danger btn-sm" href="/instalaciones/deshabilitar?id=<?= (int) $i['id'] ?>">Deshabilitar</a>
                            <?php else: ?>
                                <a class="btn btn-primary btn-sm" href="/instalaciones/habilitar?id=<?= (int) $i['id'] ?>">Habilitar</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
