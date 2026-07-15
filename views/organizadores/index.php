<div class="container">
    <div class="page-head">
        <div><div class="eyebrow">Catalogo</div><h1>Organizadores Deportivos</h1></div>
    </div>

    <?php require __DIR__ . '/../layout/_alerts.php'; ?>

    <div class="card">
        <table class="data-table">
            <thead><tr><th>Nombre</th><th>Correo</th><th>Tipo</th><th>Academia</th><th>Estado</th><th>Acciones</th></tr></thead>
            <tbody>
                <?php foreach ($organizadores as $o): ?>
                    <tr>
                        <td><?= htmlspecialchars($o['nombre_completo']) ?><?= $o['nombre_comercial'] ? ' — ' . htmlspecialchars($o['nombre_comercial']) : '' ?></td>
                        <td><?= htmlspecialchars($o['correo']) ?></td>
                        <td><?= htmlspecialchars($o['tipo_organizador']) ?></td>
                        <td><?= htmlspecialchars($o['academia_nombre'] ?? '-') ?></td>
                        <td>
                            <?php if ((int) $o['activo'] === 1): ?><span class="badge badge-success">Activo</span>
                            <?php else: ?><span class="badge badge-danger">Inactivo</span><?php endif; ?>
                        </td>
                        <td>
                            <a class="btn btn-outline btn-sm" href="/organizadores/editar?id=<?= (int) $o['id'] ?>">Editar</a>
                            <?php if (($_SESSION['usuario_rol'] ?? '') === 'ADMINISTRADOR'): ?>
                                <?php if ((int) $o['activo'] === 1): ?>
                                    <form method="POST" action="/organizadores/deshabilitar" style="display:inline">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                        <input type="hidden" name="id" value="<?= (int) $o['id'] ?>">
                                        <button class="btn btn-danger btn-sm">Deshabilitar</button>
                                    </form>
                                <?php else: ?>
                                    <form method="POST" action="/organizadores/habilitar" style="display:inline">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                        <input type="hidden" name="id" value="<?= (int) $o['id'] ?>">
                                        <button class="btn btn-primary btn-sm">Habilitar</button>
                                    </form>
                                <?php endif; ?>
                            <?php else: ?>
                                <!-- sin texto de estado redundante para roles no administradores -->
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
