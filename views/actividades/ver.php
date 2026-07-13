<div class="container">
    <div class="page-head">
        <div>
            <div class="eyebrow"><?= htmlspecialchars($actividad['tipo']) ?> · <?= htmlspecialchars($actividad['modalidad']) ?></div>
            <h1><?= htmlspecialchars($actividad['nombre']) ?></h1>
        </div>
        <a class="btn btn-outline" href="/actividades">← Volver</a>
    </div>

    <?php require __DIR__ . '/../layout/_alerts.php'; ?>

    <div class="grid-3">
        <div class="card" style="grid-column: span 2;">
            <div class="card-header-row">
                <h2>Detalle</h2>
                <span class="badge badge-neutral"><?= htmlspecialchars($actividad['estado']) ?></span>
            </div>
            <p><strong>Deporte:</strong> <?= htmlspecialchars($actividad['deporte_nombre']) ?></p>
            <p><strong>Instalacion:</strong> <?= htmlspecialchars($actividad['instalacion_nombre']) ?></p>
            <p><strong>Organizador:</strong> <?= htmlspecialchars($actividad['organizador_nombre']) ?></p>
            <p><strong>Entrenador:</strong> <?= htmlspecialchars($actividad['entrenador_nombre'] ?? 'Sin asignar') ?></p>
            <p><strong>Fecha:</strong> <?= htmlspecialchars(substr($actividad['fecha_inicio'], 0, 16)) ?> a <?= htmlspecialchars(substr($actividad['fecha_fin'], 0, 16)) ?></p>
            <p><strong>Costo:</strong> <?= (int) $actividad['requiere_pago'] === 1 ? '$' . number_format((float) $actividad['costo_inscripcion'], 2) : 'Gratuita' ?>
                &middot; <strong>Cupos:</strong> <?= $cuposOcupados ?> / <?= (int) $actividad['cupos_disponibles'] ?></p>
            <?php if ($actividad['estado'] === 'CANCELADA' && !empty($actividad['motivo_cancelacion'])): ?>
                <p><strong>Motivo de cancelacion:</strong> <?= htmlspecialchars($actividad['motivo_cancelacion']) ?></p>
            <?php endif; ?>

            <div style="margin-top:1rem;display:flex;gap:.5rem;flex-wrap:wrap;">
                <?php if ($actividad['estado'] === 'BORRADOR'): ?>
                    <a class="btn btn-amber btn-sm" href="/actividades/publicar?id=<?= (int) $actividad['id'] ?>">Publicar</a>
                <?php elseif ($actividad['estado'] === 'PUBLICADA'): ?>
                    <a class="btn btn-outline btn-sm" href="/actividades/cerrar-inscripciones?id=<?= (int) $actividad['id'] ?>">Cerrar inscripciones</a>
                    <a class="btn btn-danger btn-sm" href="/actividades/cancelar?id=<?= (int) $actividad['id'] ?>">Cancelar actividad</a>
                <?php elseif ($actividad['estado'] === 'CERRADA'): ?>
                    <a class="btn btn-primary btn-sm" href="/actividades/finalizar?id=<?= (int) $actividad['id'] ?>">Finalizar actividad</a>
                    <a class="btn btn-danger btn-sm" href="/actividades/cancelar?id=<?= (int) $actividad['id'] ?>">Cancelar actividad</a>
                <?php endif; ?>
                <a class="btn btn-outline btn-sm" href="/actividades/incidente/crear?id=<?= (int) $actividad['id'] ?>">Reportar incidente</a>
            </div>
        </div>

        <div class="qr-box">
            <h3>Codigo QR</h3>
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=<?= urlencode($urlPublica) ?>" alt="Codigo QR de la actividad">
            <p class="field-hint">Escanea para ver lugar, fecha y equipos</p>
            <a class="btn btn-outline btn-sm" href="<?= htmlspecialchars($urlPublica) ?>" target="_blank">Abrir pagina publica</a>
        </div>
    </div>

    <?php if (!empty($arbitrosAsignados)): ?>
        <div class="card">
            <h2>Arbitros asignados</h2>
            <div class="grid-3">
                <?php foreach ($arbitrosAsignados as $ar): ?>
                    <div class="event-card">
                        <strong><?= htmlspecialchars($ar['nombre_completo']) ?></strong>
                        <p class="field-hint"><?= htmlspecialchars($ar['rol']) ?> · <?= htmlspecialchars($ar['estado']) ?></p>
                        <a class="btn btn-outline btn-sm" href="/arbitros/evaluar?actividad_id=<?= (int) $actividad['id'] ?>&arbitro_id=<?= (int) $ar['arbitro_id'] ?>">Evaluar desempeno</a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($actividad['modalidad'] !== 'INDIVIDUAL'): ?>
        <div class="card">
            <div class="card-header-row">
                <h2>Inscripciones por equipo</h2>
                <a class="btn btn-primary btn-sm" href="/inscripciones/equipo/crear?actividad_id=<?= (int) $actividad['id'] ?>">+ Inscribir equipo</a>
            </div>
            <?php if (empty($inscripcionesEquipo)): ?>
                <div class="empty-state">Aun no hay equipos inscritos.</div>
            <?php else: ?>
                <table class="data-table">
                    <thead><tr><th>Equipo</th><th>Estado</th><th>Acciones</th></tr></thead>
                    <tbody>
                        <?php foreach ($inscripcionesEquipo as $ie): ?>
                            <tr>
                                <td><?= htmlspecialchars($ie['equipo_nombre']) ?></td>
                                <td>
                                    <?php
                                        $cls = match ($ie['estado']) {
                                            'APROBADA', 'FINALIZADA' => 'badge-success',
                                            'RECHAZADA', 'CANCELADA' => 'badge-danger',
                                            default => 'badge-warning',
                                        };
                                    ?>
                                    <span class="badge <?= $cls ?>"><?= htmlspecialchars($ie['estado']) ?></span>
                                </td>
                                <td>
                                    <?php if ($ie['estado'] === 'PENDIENTE_APROBACION'): ?>
                                        <a class="btn btn-primary btn-sm" href="/inscripciones/equipo/aprobar?id=<?= (int) $ie['id'] ?>">Aprobar</a>
                                        <a class="btn btn-danger btn-sm" href="/inscripciones/equipo/rechazar?id=<?= (int) $ie['id'] ?>">Rechazar</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if ($actividad['modalidad'] !== 'EQUIPO'): ?>
        <div class="card">
            <h2>Inscripciones individuales</h2>
            <?php if (empty($inscripcionesIndividual)): ?>
                <div class="empty-state">Aun no hay participantes inscritos.</div>
            <?php else: ?>
                <table class="data-table">
                    <thead><tr><th>Nombre</th><th>Correo</th><th>Estado</th></tr></thead>
                    <tbody>
                        <?php foreach ($inscripcionesIndividual as $ii): ?>
                            <tr>
                                <td><?= htmlspecialchars($ii['nombre_completo']) ?></td>
                                <td><?= htmlspecialchars($ii['correo']) ?></td>
                                <td><span class="badge badge-success"><?= htmlspecialchars($ii['estado']) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <h2>Incidentes reportados</h2>
        <?php if (empty($incidentes)): ?>
            <div class="empty-state">Sin incidentes registrados para esta actividad.</div>
        <?php else: ?>
            <table class="data-table">
                <thead><tr><th>Tipo</th><th>Gravedad</th><th>Descripcion</th><th>Fecha</th></tr></thead>
                <tbody>
                    <?php foreach ($incidentes as $inc): ?>
                        <tr>
                            <td><?= htmlspecialchars($inc['tipo']) ?></td>
                            <td><span class="badge badge-warning"><?= htmlspecialchars($inc['gravedad']) ?></span></td>
                            <td><?= htmlspecialchars($inc['descripcion']) ?></td>
                            <td><?= htmlspecialchars($inc['fecha_incidente']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
