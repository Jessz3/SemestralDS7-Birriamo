<div class="container">
    <div class="page-head">
        <div><div class="eyebrow">Participa</div><h1>Actividades vigentes</h1><p>Actividades en curso o próximas que todavía admiten inscripciones.</p></div>
    </div>
    <?php require __DIR__ . '/../layout/_alerts.php'; ?>

    <?php if (empty($actividades)): ?>
        <div class="card empty-state">No hay actividades disponibles en este momento.</div>
    <?php else: ?>
        <div class="grid-3">
            <?php foreach ($actividades as $a): ?>
                <div class="event-card">
                    <div class="card-header-row">
                        <span class="badge badge-success"><?= htmlspecialchars($a['modalidad']) ?></span>
                        <span class="badge badge-neutral"><?= htmlspecialchars($a['tipo']) ?></span>
                    </div>
                    <h2><?= htmlspecialchars($a['nombre']) ?></h2>
                    <p><?= htmlspecialchars($a['descripcion']) ?></p>
                    <p><strong><?= htmlspecialchars($a['deporte_nombre']) ?></strong></p>
                    <p><?= htmlspecialchars($a['instalacion_nombre']) ?> · <?= htmlspecialchars($a['instalacion_direccion']) ?></p>
                    <p class="field-hint"><?= htmlspecialchars(substr($a['fecha_inicio'], 0, 16)) ?> a <?= htmlspecialchars(substr($a['fecha_fin'], 0, 16)) ?></p>
                    <p><strong><?= (int) $a['requiere_pago'] === 1 ? '$' . number_format((float) $a['costo_inscripcion'], 2) : 'Gratuita' ?></strong></p>
                    <a class="btn btn-primary" href="<?= BASE_URL ?>/actividades/ver?id=<?= (int) $a['id'] ?>">Ver e inscribirme</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
