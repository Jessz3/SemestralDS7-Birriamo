<div class="container">
    <div class="page-head">
        <div>
            <div class="eyebrow">Catalogo</div>
            <h1>Disciplinas Deportivas</h1>
        </div>
        <a class="btn btn-primary" href="/deportes/crear">+ Nuevo deporte</a>
    </div>

    <?php require __DIR__ . '/../layout/_alerts.php'; ?>

    <div class="card">
        <table class="data-table">
            <thead><tr><th>Nombre</th><th>Descripcion</th><th>Tipo</th><th>Jugadores</th><th>Estado</th><th>Acciones</th></tr></thead>
            <tbody>
                <?php foreach ($deportes as $d): ?>
                    <tr>
                        <td><?= htmlspecialchars($d['nombre']) ?></td>
                        <td><?= htmlspecialchars($d['descripcion'] ?? '-') ?></td>
                        <td><?= (int) $d['es_equipo'] === 1 ? 'Por equipo' : 'Individual' ?></td>
                        <td><?= (int) $d['es_equipo'] === 1 ? ($d['minimo_jugadores'] ?? '?') . ' - ' . ($d['maximo_jugadores'] ?? '?') : '-' ?></td>
                        <td>
                            <?php if ((int) $d['activo'] === 1): ?>
                                <span class="badge badge-success">Activo</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a class="btn btn-outline btn-sm" href="/deportes/editar?id=<?= (int) $d['id'] ?>">Editar</a>
                            <?php if ((int) $d['activo'] === 1): ?>
                                <a class="btn btn-danger btn-sm" href="/deportes/deshabilitar?id=<?= (int) $d['id'] ?>">Deshabilitar</a>
                            <?php else: ?>
                                <a class="btn btn-primary btn-sm" href="/deportes/habilitar?id=<?= (int) $d['id'] ?>">Habilitar</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
