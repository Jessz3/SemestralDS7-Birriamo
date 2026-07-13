<div class="container">
    <div class="page-head">
        <div><div class="eyebrow">Gestion operativa</div><h1>Actividades Deportivas</h1></div>
        <a class="btn btn-primary" href="/actividades/crear">+ Nueva actividad</a>
    </div>

    <?php require __DIR__ . '/../layout/_alerts.php'; ?>

    <div class="card">
        <table class="data-table">
            <thead><tr><th>Nombre</th><th>Deporte</th><th>Fecha inicio</th><th>Instalacion</th><th>Estado</th><th>Acciones</th></tr></thead>
            <tbody>
                <?php foreach ($actividades as $a): ?>
                    <tr>
                        <td><?= htmlspecialchars($a['nombre']) ?></td>
                        <td><?= htmlspecialchars($a['deporte_nombre']) ?></td>
                        <td><?= htmlspecialchars(substr($a['fecha_inicio'], 0, 16)) ?></td>
                        <td><?= htmlspecialchars($a['instalacion_nombre']) ?></td>
                        <td>
                            <?php
                                $badgeClass = match ($a['estado']) {
                                    'FINALIZADA' => 'badge-success',
                                    'CANCELADA' => 'badge-danger',
                                    'PUBLICADA' => 'badge-success',
                                    'CERRADA' => 'badge-warning',
                                    default => 'badge-neutral',
                                };
                            ?>
                            <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($a['estado']) ?></span>
                        </td>
                        <td>
                            <a class="btn btn-outline btn-sm" href="/actividades/ver?id=<?= (int) $a['id'] ?>">Ver</a>
                            <a class="btn btn-outline btn-sm" href="/actividades/editar?id=<?= (int) $a['id'] ?>">Editar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
