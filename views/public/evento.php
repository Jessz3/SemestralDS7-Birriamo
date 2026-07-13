<div class="section-public">
    <span class="event-card tag" style="display:inline-block;"><?= htmlspecialchars($actividad['deporte_nombre']) ?> · <?= htmlspecialchars($actividad['tipo']) ?></span>
    <h1><?= htmlspecialchars($actividad['nombre']) ?></h1>
    <p class="field-hint"><?= nl2br(htmlspecialchars($actividad['descripcion'] ?? '')) ?></p>

    <?php if (!$admiteInscripcion): ?>
        <div class="alert alert-danger">
            Esta actividad ya no admite inscripciones.
        </div>
    <?php endif; ?>

    <div class="grid-3" style="margin-top:1rem;">
        <div class="card">
            <strong>📅 Fecha y hora</strong>
            <p><?= htmlspecialchars(substr($actividad['fecha_inicio'], 0, 16)) ?> — <?= htmlspecialchars(substr($actividad['fecha_fin'], 0, 16)) ?></p>
        </div>
        <div class="card">
            <strong>📍 Lugar</strong>
            <p><?= htmlspecialchars($actividad['instalacion_nombre']) ?><br>
                <span class="field-hint"><?= htmlspecialchars($actividad['instalacion_direccion']) ?></span></p>
        </div>
        <div class="card">
            <strong>💰 Costo de inscripcion</strong>
            <p><?= (int) $actividad['requiere_pago'] === 1 ? '$' . number_format((float) $actividad['costo_inscripcion'], 2) : 'Gratuita' ?></p>
        </div>
    </div>

    <?php if ($actividad['modalidad'] !== 'INDIVIDUAL'): ?>
        <div class="card" style="margin-top:1.5rem;">
            <h2>Equipos participantes</h2>
            <?php if (empty($equipos)): ?>
                <p class="field-hint">Aun no hay equipos aprobados para esta actividad.</p>
            <?php else: ?>
                <div class="grid-3">
                    <?php foreach ($equipos as $eq): ?>
                        <div class="event-card"><strong><?= htmlspecialchars($eq['equipo_nombre']) ?></strong></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if ($actividad['modalidad'] !== 'EQUIPO' && $admiteInscripcion): ?>
        <div class="card" style="margin-top:1.5rem;">
            <h2>Inscripcion individual</h2>
            <p class="field-hint">Esta actividad admite participacion individual.</p>
            <a class="btn btn-primary" href="/inscripciones/individual/crear?actividad_id=<?= (int) $actividad['id'] ?>">Inscribirme ahora</a>
        </div>
    <?php endif; ?>
</div>
