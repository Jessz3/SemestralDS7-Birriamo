<script src="<?= BASE_URL ?>/assets/js/password-toggle.js"></script>

<div class="hero" style="padding-bottom:2rem;">
    <span class="badge-dot" style="display:inline-block;width:10px;height:10px;border-radius:50%;margin-bottom:.5rem;"></span>
    <h1>Iniciar Sesion</h1>
    <p>Acceso administrativo y operativo del Sistema de Eventos Deportivos.</p>
</div>

<div class="section-public" style="max-width:420px;">
    <div class="card">
        <?php require __DIR__ . '/../layout/_alerts.php'; ?>

        <form method="POST" action="/login">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">

            <div class="field">
                <label for="usuario">Usuario</label>
                <input type="text" id="usuario" name="usuario" required autofocus>
            </div>

            <?php
            /*Sección de entrada de contraseña con toggle*/
            $id='password';
            $name='password';
            $label='Contraseña';
            require ROOT_PATH.'/views/components/password-input.php';
            ?>

            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">Ingresar</button>
        </form>

        <p style="margin-top:1rem;text-align:center;">
            ¿No tiene una cuenta? <a href="<?= BASE_URL ?>/registro">Regístrese como organizador o participante</a>
        </p>

        <p class="field-hint" style="margin-top:1rem;text-align:center;">
            Usuario de prueba: <code>admin</code> — contrasena <code>Admin123</code>
        </p>
    </div>
</div>
