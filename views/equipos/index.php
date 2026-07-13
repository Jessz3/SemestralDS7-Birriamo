<div class="container">
    <div class="page-head">
        <div><div class="eyebrow">Inscripciones por equipo</div><h1><?= ($_SESSION['usuario_rol'] ?? '') === 'PARTICIPANTE' ? 'Mis Equipos' : 'Equipos Registrados' ?></h1></div>
        <a class="btn btn-primary" href="/equipos/crear">+ Nuevo equipo</a>
    </div>

    <?php require __DIR__ . '/../layout/_alerts.php'; ?>

    <div class="card">
        <table class="data-table">
            <thead><tr><th></th><th>Nombre</th><th>Deporte</th><th>Academia</th><th>Representante</th><th>Jugadores</th><th>Acciones</th></tr></thead>
            <tbody>
                <?php if (empty($equipos)): ?><tr><td colspan="7" class="empty-state">Todavía no tienes equipos registrados.</td></tr><?php endif; ?>
                <?php foreach ($equipos as $e): ?>
                    <tr>
                        <td style="width:44px;">
                            <?php if (!empty($e['avatar'])): ?>
                                <img src="<?= htmlspecialchars($e['avatar']) ?>" alt="" style="width:36px;height:36px;border-radius:50%;object-fit:cover;">
                            <?php else: ?>
                                <div style="width:36px;height:36px;border-radius:50%;background:#e1e6ec;"></div>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($e['nombre']) ?></td>
                        <td><?= htmlspecialchars($e['deporte_nombre']) ?></td>
                        <td><?= htmlspecialchars($e['academia_nombre'] ?? 'Independiente') ?></td>
                        <td><?= htmlspecialchars($e['representante']) ?></td>
                        <td><?= (int) $e['total_jugadores'] ?></td>
                        <td><a class="btn btn-outline btn-sm" href="/equipos/ver?id=<?= (int) $e['id'] ?>">Ver plantilla</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
