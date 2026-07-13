<div class="container">
    <div class="page-head">
        <div>
            <div class="eyebrow">Mi espacio deportivo</div>
            <h1>Hola, <?= htmlspecialchars($_SESSION['usuario_nombre'] ?? '') ?></h1>
            <p>Consulta actividades vigentes e inscríbete individualmente o con uno de tus equipos.</p>
        </div>
    </div>

    <?php require __DIR__ . '/../layout/_alerts.php'; ?>

    <div class="grid-3">
        <div class="stat-card"><div class="value"><?= count($actividades) ?></div><div class="label">Actividades vigentes</div></div>
        <div class="stat-card"><div class="value"><?= count($equipos) ?></div><div class="label">Mis equipos</div></div>
        <div class="stat-card"><div class="value"><?= count($inscripcionesIndividuales) + count($inscripcionesEquipo) ?></div><div class="label">Mis inscripciones</div></div>
    </div>

    <div class="card" style="margin-top:1.5rem;">
        <div class="card-header-row">
            <h2>Próximas actividades y actividades en curso</h2>
            <a class="btn btn-outline btn-sm" href="<?= BASE_URL ?>/actividades">Ver todas</a>
        </div>
        <?php if (empty($actividades)): ?>
            <div class="empty-state">No hay actividades abiertas para inscripción en este momento.</div>
        <?php else: ?>
            <div class="grid-3">
                <?php foreach (array_slice($actividades, 0, 6) as $a): ?>
                    <div class="event-card">
                        <span class="badge badge-success"><?= htmlspecialchars($a['modalidad']) ?></span>
                        <h3><?= htmlspecialchars($a['nombre']) ?></h3>
                        <p><?= htmlspecialchars($a['deporte_nombre']) ?> · <?= htmlspecialchars($a['instalacion_nombre']) ?></p>
                        <p class="field-hint"><?= htmlspecialchars(substr($a['fecha_inicio'], 0, 16)) ?></p>
                        <a class="btn btn-primary btn-sm" href="<?= BASE_URL ?>/actividades/ver?id=<?= (int) $a['id'] ?>">Ver e inscribirme</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="card">
        <div class="card-header-row"><h2>Mis equipos</h2><a class="btn btn-primary btn-sm" href="<?= BASE_URL ?>/equipos/crear">+ Crear equipo</a></div>
        <?php if (empty($equipos)): ?>
            <div class="empty-state">Todavía no has creado equipos. Puedes representar una academia o competir de forma independiente.</div>
        <?php else: ?>
            <?php foreach ($equipos as $equipo): ?>
                <a class="btn btn-outline btn-sm" href="<?= BASE_URL ?>/equipos/ver?id=<?= (int) $equipo['id'] ?>"><?= htmlspecialchars($equipo['nombre']) ?></a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="card">
        <h2>Mis inscripciones</h2>
        <?php if (empty($inscripcionesIndividuales) && empty($inscripcionesEquipo)): ?><div class="empty-state">Aún no tienes inscripciones.</div><?php endif; ?>
        <?php foreach ($inscripcionesIndividuales as $inscripcion): ?>
            <p><strong><?= htmlspecialchars($inscripcion['actividad_nombre']) ?></strong> · Individual · <span class="badge badge-neutral"><?= htmlspecialchars($inscripcion['estado']) ?></span></p>
        <?php endforeach; ?>
        <?php foreach ($inscripcionesEquipo as $inscripcion): ?>
            <p><strong><?= htmlspecialchars($inscripcion['actividad_nombre']) ?></strong> · <?= htmlspecialchars($inscripcion['equipo_nombre']) ?> · <span class="badge badge-neutral"><?= htmlspecialchars($inscripcion['estado']) ?></span></p>
        <?php endforeach; ?>
    </div>
</div>
