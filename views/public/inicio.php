<div class="hero">
    <span class="badge-dot" style="display:inline-block;width:10px;height:10px;border-radius:50%;margin-bottom:.6rem;"></span>
    <h1>Sistema de Eventos Deportivos</h1>
    <p>Organiza, inscríbete y da seguimiento a torneos, entrenamientos y eventos deportivos de la comunidad.</p>
</div>

<section class="section-public" id="actividades">
    <h2>Actividades disponibles</h2>
    <p>Consulta las actividades próximas y las que están en curso con inscripciones abiertas.</p>

    <?php if (empty($actividades)): ?>
        <div class="empty-state">Por el momento no hay actividades vigentes con inscripciones abiertas. Vuelve pronto.</div>
    <?php else: ?>
        <div class="grid-3">
            <?php foreach ($actividades as $act): ?>
                <div class="event-card">
                    <div class="card-header-row">
                        <span class="tag"><?= htmlspecialchars($act['deporte_nombre']) ?></span>
                        <span class="badge <?= strtotime($act['fecha_inicio']) <= time() ? 'badge-success' : 'badge-neutral' ?>">
                            <?= strtotime($act['fecha_inicio']) <= time() ? 'EN CURSO' : 'PRÓXIMA' ?>
                        </span>
                    </div>
                    <h3><?= htmlspecialchars($act['nombre']) ?></h3>
                    <p><?= htmlspecialchars($act['descripcion']) ?></p>
                    <p class="field-hint">
                        Fecha: <?= htmlspecialchars(substr($act['fecha_inicio'], 0, 16)) ?><br>
                        Lugar: <?= htmlspecialchars($act['instalacion_nombre']) ?>
                    </p>
                    <p><span class="badge badge-neutral"><?= htmlspecialchars($act['modalidad']) ?></span></p>
                    <p>
                        <?= (int) $act['requiere_pago'] === 1
                            ? 'Inscripción: <strong>$' . number_format((float) $act['costo_inscripcion'], 2) . '</strong>'
                            : '<strong>Gratuita</strong>' ?>
                    </p>
                    <a class="btn btn-primary btn-sm" href="<?= BASE_URL ?>/evento/<?= htmlspecialchars($act['token_publico']) ?>">Ver detalle e inscribirme</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<section class="section-public" id="stack" style="background:#fff;border-radius:12px;">
    <h2>Nuestro stack tecnológico</h2>
    <div class="grid-4">
        <div class="card"><strong>Backend</strong><p class="field-hint">PHP 8.1+, arquitectura MVC y autoload PSR-4.</p></div>
        <div class="card"><strong>Base de datos</strong><p class="field-hint">MySQL/MariaDB con PDO y sentencias preparadas.</p></div>
        <div class="card"><strong>Seguridad</strong><p class="field-hint">OWASP, HMAC-SHA256, RSA/OpenSSL y contraseñas con hash.</p></div>
        <div class="card"><strong>Reportes</strong><p class="field-hint">TCPDF para facturación con firma digital y hash SHA-256.</p></div>
    </div>
</section>

<section class="section-public" id="importancia">
    <h2>¿Por qué este sistema?</h2>
    <div class="card">
        <p>Centraliza la organización de actividades deportivas, la inscripción de equipos y participantes, los pagos y la evaluación de árbitros. Cada acción relevante queda registrada para garantizar la integridad de la información.</p>
    </div>
</section>

<section class="section-public" id="contacto">
    <h2>Contáctenos</h2>
    <?php require __DIR__ . '/../layout/_alerts.php'; ?>

    <div class="card" style="max-width:560px;">
        <form method="POST" action="<?= BASE_URL ?>/contacto">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
            <div class="grid-2">
                <div class="field"><label>Nombre</label><input type="text" name="nombre" minlength="2" maxlength="160" required></div>
                <div class="field"><label>Correo electrónico</label><input type="email" name="correo" maxlength="150" required></div>
            </div>
            <div class="field"><label>Teléfono (opcional)</label><input type="tel" name="telefono" pattern="(?:6[0-9]{7}|[2-9][0-9]{6})" maxlength="8" inputmode="numeric" placeholder="61234567" title="Ingrese un celular de 8 digitos que inicie en 6 o un telefono fijo panameno de 7 digitos"></div>
            <div class="field"><label>Asunto</label><input type="text" name="asunto" minlength="3" maxlength="180" required></div>
            <div class="field"><label>Mensaje</label><textarea name="mensaje" rows="4" minlength="10" maxlength="2000" required></textarea></div>
            <button type="submit" class="btn btn-primary">Enviar mensaje</button>
        </form>
    </div>
</section>
