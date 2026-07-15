<div class="container">
    <div class="page-head">
        <div>
            <div class="eyebrow"><?= htmlspecialchars($equipo['deporte_nombre']) ?></div>
            <h1><?= htmlspecialchars($equipo['nombre']) ?></h1>
        </div>
        <a class="btn btn-outline" href="/equipos">← Volver</a>
    </div>

    <?php require __DIR__ . '/../layout/_alerts.php'; ?>

    <div class="grid-2">
        <div class="card">
            <h2>Datos del equipo</h2>
            <?php if (!empty($equipo['avatar'])): ?>
                <img src="<?= htmlspecialchars($equipo['avatar']) ?>" alt="" style="width:80px;height:80px;border-radius:50%;object-fit:cover;margin-bottom:.75rem;">
            <?php endif; ?>
            <p><strong>Representante:</strong> <?= htmlspecialchars($equipo['representante']) ?></p>
            <p><strong>Correo:</strong> <?= htmlspecialchars($equipo['correo_contacto']) ?></p>
            <p><strong>Academia:</strong> <?= htmlspecialchars($equipo['academia_nombre'] ?? 'Independiente') ?></p>
        </div>

        <div class="card">
            <h2>Agregar jugador</h2>
            <form method="POST" action="/equipos/jugadores/agregar">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                <input type="hidden" name="equipo_id" value="<?= (int) $equipo['id'] ?>">

                <div class="field">
                    <label>Nombre completo</label>
                    <input type="text" name="nombre_completo" minlength="3" maxlength="160" required>
                </div>
                <div class="grid-2">
                    <div class="field">
                        <label>Edad</label>
                        <input type="number" name="edad" min="5" max="80" step="1" required>
                    </div>
                    <div class="field">
                        <label>Peso (kg)</label>
                        <input type="number" step="0.1" name="peso_kg" min="1" max="500">
                    </div>
                </div>
                <div class="grid-2">
                    <div class="field">
                        <label>Posicion</label>
                        <input type="text" name="posicion" maxlength="80">
                    </div>
                    <div class="field">
                        <label>Numero de camiseta</label>
                        <input type="number" name="numero_camiseta" min="0" max="99" step="1">
                    </div>
                </div>
                <div class="field">
                    <label style="font-weight:400;"><input type="checkbox" name="capitan" value="1" style="width:auto;display:inline-block;"> Es el capitan del equipo</label>
                </div>
                <button type="submit" class="btn btn-primary btn-sm">Agregar jugador</button>
            </form>
        </div>
    </div>

    <div class="card">
        <h2>Plantilla de jugadores</h2>
        <?php if (empty($jugadores)): ?>
            <div class="empty-state">Este equipo aun no tiene jugadores registrados.</div>
        <?php else: ?>
            <table class="data-table">
                <thead><tr><th>#</th><th>Nombre</th><th>Edad</th><th>Peso</th><th>Posicion</th><th></th></tr></thead>
                <tbody>
                    <?php foreach ($jugadores as $j): ?>
                        <tr>
                            <td><?= $j['numero_camiseta'] ?? '-' ?></td>
                            <td><?= htmlspecialchars($j['nombre_completo']) ?><?= (int) $j['capitan'] === 1 ? ' <span class="badge badge-warning">Capitan</span>' : '' ?></td>
                            <td><?= (int) $j['edad'] ?></td>
                            <td><?= $j['peso_kg'] ? htmlspecialchars($j['peso_kg']) . ' kg' : '-' ?></td>
                            <td><?= htmlspecialchars($j['posicion'] ?? '-') ?></td>
                            <td>
                                <a class="btn btn-danger btn-sm" href="/equipos/jugadores/eliminar?equipo_id=<?= (int) $equipo['id'] ?>&jugador_id=<?= (int) $j['id'] ?>&csrf_token=<?= urlencode($csrf) ?>" onclick="return confirm('¿Eliminar jugador?')">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
