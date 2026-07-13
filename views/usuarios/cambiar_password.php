<div class="container" style="max-width:480px;">
    <div class="page-head">
        <div>
            <div class="eyebrow">Mi cuenta</div>
            <h1>Cambiar contrasena</h1>
        </div>
    </div>

    <?php require __DIR__ . '/../layout/_alerts.php'; ?>

    <div class="card">
        <form method="POST" action="/mi-cuenta/password">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">

            <div class="field">
                <label>Contrasena actual</label>
                <input type="password" name="password_actual" required>
            </div>
            <div class="field">
                <label>Nueva contrasena</label>
                <input type="password" name="password_nueva" required>
            </div>
            <div class="field">
                <label>Confirmar nueva contrasena</label>
                <input type="password" name="password_confirmacion" required>
            </div>

            <button type="submit" class="btn btn-primary">Actualizar contrasena</button>
        </form>
    </div>
</div>
