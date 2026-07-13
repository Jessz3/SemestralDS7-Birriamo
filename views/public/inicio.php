<div class="hero">
    <span class="badge-dot" style="display:inline-block;width:10px;height:10px;border-radius:50%;margin-bottom:.6rem;"></span>
    <h1>Sistema de Eventos Deportivos</h1>
    <p>Organiza, inscribe y da seguimiento a torneos, entrenamientos y eventos deportivos de la comunidad, con integridad y trazabilidad garantizadas.</p>
</div>

<section class="section-public" id="actividades">
    <h2>Actividades disponibles</h2>

    <?php if (empty($actividades)): ?>
        <div class="empty-state">Por el momento no hay actividades publicadas. Vuelve pronto.</div>
    <?php else: ?>
        <div class="grid-3">
            <?php foreach ($actividades as $act): ?>
                <div class="event-card">
                    <span class="tag"><?= htmlspecialchars($act['deporte']) ?></span>
                    <h3><?= htmlspecialchars($act['nombre']) ?></h3>
                    <p class="field-hint">
                        📅 <?= htmlspecialchars(substr($act['fecha_inicio'], 0, 16)) ?>
                        <br>📍 <?= htmlspecialchars($act['instalacion']) ?>
                    </p>
                    <p style="font-size:.85rem;">
                        <?php if ((int) $act['requiere_pago'] === 1): ?>
                            Inscripcion: <strong>$<?= number_format((float) $act['costo_inscripcion'], 2) ?></strong>
                        <?php else: ?>
                            <strong>Gratuita</strong>
                        <?php endif; ?>
                    </p>
                    <a class="btn btn-primary btn-sm" href="/evento/<?= $act['token_publico'] ?>">Ver detalle</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<section class="section-public" id="stack" style="background:#fff;border-radius:12px;">
    <h2>Nuestro Stack Tecnologico</h2>
    <div class="grid-4">
        <div class="card"><strong>Backend</strong><p class="field-hint">PHP 8.1+, arquitectura MVC, PSR-1/PSR-4 via Composer.</p></div>
        <div class="card"><strong>Base de Datos</strong><p class="field-hint">MySQL/MariaDB con PDO y sentencias preparadas (30 tablas + vistas).</p></div>
        <div class="card"><strong>Seguridad</strong><p class="field-hint">OWASP, HMAC-SHA256, RSA/OpenSSL, hashing BCRYPT, bitacora firmada.</p></div>
        <div class="card"><strong>Reportes</strong><p class="field-hint">TCPDF para facturacion con firma digital y hash SHA-256.</p></div>
    </div>
</section>

<section class="section-public" id="importancia">
    <h2>¿Por qué este sistema?</h2>
    <div class="card">
        <p>Centraliza la organización de actividades deportivas comunitarias — torneos, entrenamientos y eventos — resolviendo de forma trazable la inscripción de equipos y participantes, el cobro con ITBMS, y la evaluación de árbitros e instalaciones. Cada acción relevante queda registrada en una bitácora firmada digitalmente, garantizando la integridad de la información ante cualquier auditoría.</p>
    </div>
</section>

<section class="section-public" id="contacto">
    <h2>Contactenos</h2>

    <?php require __DIR__ . '/../layout/_alerts.php'; ?>

    <div class="card" style="max-width:560px;">
        <form method="POST" action="/contacto">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">

            <div class="grid-2">
                <div class="field">
                    <label>Nombre</label>
                    <input type="text" name="nombre" required>
                </div>
                <div class="field">
                    <label>Correo electronico</label>
                    <input type="email" name="correo" required>
                </div>
            </div>
            <div class="field">
                <label>Telefono (opcional)</label>
                <input type="text" name="telefono">
            </div>
            <div class="field">
                <label>Asunto</label>
                <input type="text" name="asunto" required>
            </div>
            <div class="field">
                <label>Mensaje</label>
                <textarea name="mensaje" rows="4" required></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Enviar mensaje</button>
        </form>

        <p class="field-hint" style="margin-top:1rem;">
            También puedes escribirnos directamente a fisc<?= '' ?>@utp.ac.pa &nbsp;|&nbsp; (507) 560-3000
        </p>
    </div>
</section>
