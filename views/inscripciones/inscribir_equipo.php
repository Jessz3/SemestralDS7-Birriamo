<div class="container" style="max-width:560px;">
    <div class="page-head"><div><div class="eyebrow">Inscripcion por equipo</div><h1><?= htmlspecialchars($actividad['nombre']) ?></h1></div></div>

    <?php require __DIR__ . '/../layout/_alerts.php'; ?>

    <div class="card">
        <p class="field-hint">
            <?= (int) $actividad['requiere_pago'] === 1
                ? 'Costo de inscripcion: <strong>$' . number_format((float) $actividad['costo_inscripcion'], 2) . '</strong>'
                : '<strong>Actividad gratuita</strong>' ?>
        </p>
        <form method="POST" action="/inscripciones/equipo/crear">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
            <input type="hidden" name="actividad_id" value="<?= (int) $actividad['id'] ?>">

            <div class="field">
                <label>Equipo</label>
                <?php if (empty($equipos)): ?>
                    <div class="alert alert-danger">No tienes un equipo de <?= htmlspecialchars($actividad['deporte_nombre']) ?> disponible.</div>
                    <a class="btn btn-outline" href="<?= BASE_URL ?>/equipos/crear">Crear equipo</a>
                <?php else: ?>
                <select name="equipo_id" required>
                    <?php foreach ($equipos as $e): ?>
                        <option value="<?= (int) $e['id'] ?>"><?= htmlspecialchars($e['nombre']) ?> (<?= htmlspecialchars($e['deporte_nombre']) ?>)</option>
                    <?php endforeach; ?>
                </select>
                <?php endif; ?>
            </div>

            <?php if (!empty($equipos)): ?><button type="submit" class="btn btn-primary">Confirmar inscripcion</button><?php endif; ?>
            <a class="btn btn-outline" href="/actividades/ver?id=<?= (int) $actividad['id'] ?>">Cancelar</a>
        </form>
    </div>
</div>
