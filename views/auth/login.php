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

            <div class="field">
                <label for="password">Contraseña</label>

                <div class="password-input-wrapper">
                    <input
                        type="password"
                        name="password"
                        id="password"
                        required
                        autocomplete="current-password"
                    >

                    <button
                        type="button"
                        class="password-toggle-btn"
                        data-toggle-password="password"
                        aria-label="Mostrar u ocultar contraseña"
                        aria-pressed="false"
                    >
                        <svg class="eye-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>

                        <svg class="eye-off-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 3l18 18"></path>
                            <path d="M10.6 10.6A3 3 0 0 0 13.4 13.4"></path>
                            <path d="M9 5.3A10.9 10.9 0 0 1 12 5c6.5 0 10 7 10 7"></path>
                            <path d="M6.3 6.3A18.7 18.7 0 0 0 2 12s3.5 7 10 7a10.9 10.9 0 0 0 4.9-1.2"></path>
                        </svg>
                    </button>
                </div>
            </div>

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

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.password-toggle-btn').forEach(function (button) {
        button.addEventListener('click', function () {
            const targetId = this.getAttribute('data-toggle-password');
            const input = document.getElementById(targetId);
            if (!input) return;

            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';

            this.classList.toggle('is-visible', isPassword);
            this.setAttribute('aria-pressed', String(isPassword));
        });
    });
});
</script>
