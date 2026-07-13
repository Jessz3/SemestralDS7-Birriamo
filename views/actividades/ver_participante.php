<div class="container" style="max-width:900px;">
    <div class="page-head">
        <div><div class="eyebrow"><?= htmlspecialchars($actividad['tipo']) ?> · <?= htmlspecialchars($actividad['modalidad']) ?></div><h1><?= htmlspecialchars($actividad['nombre']) ?></h1></div>
        <a class="btn btn-outline" href="<?= BASE_URL ?>/actividades">← Volver</a>
    </div>
    <?php require __DIR__ . '/../layout/_alerts.php'; ?>

    <div class="grid-2">
        <div class="card">
            <h2>Información de la actividad</h2>
            <p><?= nl2br(htmlspecialchars($actividad['descripcion'])) ?></p>
            <p><strong>Deporte:</strong> <?= htmlspecialchars($actividad['deporte_nombre']) ?></p>
            <p><strong>Lugar:</strong> <?= htmlspecialchars($actividad['instalacion_nombre']) ?>, <?= htmlspecialchars($actividad['instalacion_direccion']) ?></p>
            <p><strong>Fecha:</strong> <?= htmlspecialchars(substr($actividad['fecha_inicio'], 0, 16)) ?> a <?= htmlspecialchars(substr($actividad['fecha_fin'], 0, 16)) ?></p>
            <p><strong>Organiza:</strong> <?= htmlspecialchars($actividad['organizador_nombre']) ?></p>
            <p><strong>Costo:</strong> <?= (int) $actividad['requiere_pago'] === 1 ? '$' . number_format((float) $actividad['costo_inscripcion'], 2) : 'Gratuita' ?></p>
            <p><strong>Cupos:</strong> <?= (int) $cuposOcupados ?> / <?= (int) $actividad['cupos_disponibles'] ?></p>
            <?php if (!empty($actividad['reglas'])): ?><p><strong>Reglas:</strong><br><?= nl2br(htmlspecialchars($actividad['reglas'])) ?></p><?php endif; ?>
        </div>

        <div class="card">
            <h2>Inscribirme</h2>
            <?php if (!$admiteInscripcion): ?>
                <div class="alert alert-danger">Las inscripciones para esta actividad están cerradas.</div>
            <?php else: ?>
                <?php if ($actividad['modalidad'] !== 'EQUIPO'): ?>
                    <?php if ($inscripcionIndividual): ?>
                        <p>Inscripción individual: <span class="badge badge-success"><?= htmlspecialchars($inscripcionIndividual['estado']) ?></span></p>
                    <?php else: ?>
                        <a class="btn btn-primary" href="<?= BASE_URL ?>/inscripciones/individual/crear?actividad_id=<?= (int) $actividad['id'] ?>">Inscribirme individualmente</a>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if ($actividad['modalidad'] !== 'INDIVIDUAL'): ?>
                    <h3 style="margin-top:1rem;">Inscripción por equipo</h3>
                    <?php if (empty($equipos)): ?>
                        <p>Primero debes crear un equipo, ya sea independiente o en representación de una academia.</p>
                        <a class="btn btn-outline" href="<?= BASE_URL ?>/equipos/crear">Crear mi equipo</a>
                    <?php else: ?>
                        <a class="btn btn-primary" href="<?= BASE_URL ?>/inscripciones/equipo/crear?actividad_id=<?= (int) $actividad['id'] ?>">Inscribir uno de mis equipos</a>
                    <?php endif; ?>
                    <?php foreach ($inscripcionesEquipo as $inscripcion): ?>
                        <p><?= htmlspecialchars($inscripcion['equipo_nombre']) ?>: <span class="badge badge-neutral"><?= htmlspecialchars($inscripcion['estado']) ?></span></p>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
