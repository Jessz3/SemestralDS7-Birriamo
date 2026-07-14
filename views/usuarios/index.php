<div class="container">
    <div class="page-head">
        <div>
            <div class="eyebrow">Administracion</div>
            <h1>Usuarios del Sistema</h1>
        </div>
        <a class="btn btn-primary" href="/usuarios/crear">+ Nuevo usuario</a>
    </div>

    <?php require __DIR__ . '/../layout/_alerts.php'; ?>

    <div class="card">
        <table class="data-table">
            <thead>
                <tr><th>Nombre</th><th>Usuario</th><th>Correo</th><th>Rol</th><th>Estado</th><th>Acciones</th></tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $u): ?>
                    <tr>
                        <td><?= htmlspecialchars($u['nombre'] . ' ' . $u['apellido']) ?></td>
                        <td><?= htmlspecialchars($u['usuario']) ?></td>
                        <td><?= htmlspecialchars($u['correo']) ?></td>
                        <td><span class="badge badge-neutral"><?= htmlspecialchars($u['rol']) ?></span></td>
                        <td>
                            <?php if ((int) $u['activo'] === 1): ?>
                                <span class="badge badge-success">Activo</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Deshabilitado</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a class="btn btn-outline btn-sm" href="/usuarios/editar?id=<?= (int) $u['id'] ?>">Editar</a>
                            <?php if ((int) $u['activo'] === 1): ?>
                                <a class="btn btn-danger btn-sm" href="/usuarios/deshabilitar?id=<?= (int) $u['id'] ?>&csrf_token=<?= urlencode($csrf) ?>" onclick="return confirm('¿Deshabilitar este usuario?')">Deshabilitar</a>
                            <?php else: ?>
                                <a class="btn btn-primary btn-sm" href="/usuarios/habilitar?id=<?= (int) $u['id'] ?>&csrf_token=<?= urlencode($csrf) ?>">Habilitar</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
