<div class="container">
    <div class="page-head">
        <div><div class="eyebrow">Catalogo</div><h1>Entrenadores</h1></div>
        <?php if (in_array($_SESSION['usuario_rol'] ?? '', ['ADMINISTRADOR', 'OPERADOR'], true)): ?>
            <a class="btn btn-primary" href="<?= BASE_URL ?>/entrenadores/crear">+ Nuevo entrenador</a>
        <?php endif; ?>
    </div>

    <?php require __DIR__ . '/../layout/_alerts.php'; ?>

    <div class="card">
        <table class="data-table">
            <thead><tr><th>Nombre</th><th>Correo</th><th>Telefono</th><th>Academia</th><th>Años de experiencia</th></tr></thead>
            <tbody>
                <?php foreach ($entrenadores as $e): ?>
                    <tr>
                        <td><?= htmlspecialchars($e['nombre_completo']) ?></td>
                        <td><?= htmlspecialchars($e['correo'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($e['telefono'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($e['academia_nombre'] ?? 'Independiente') ?></td>
                        <td><?= htmlspecialchars($e['anios_experiencia'] ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
