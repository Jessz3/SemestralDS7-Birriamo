<script src="<?= BASE_URL ?>/assets/js/password-toggle.js"></script>

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

            <!--Sección de entrada de contraseña con toggle-->
            <?php
            $id='password_actual';
            $name='password_actual';
            $label='Contraseña actual';

            require ROOT_PATH.'/views/components/password-input.php';
            ?>

            <!--Sección de entrada de nueva contraseña con toggle-->
            <?php
            $id='password_nueva';
            $name='password_nueva';
            $label='Nueva contraseña';

            require ROOT_PATH.'/views/components/password-input.php';
            ?>

            <!--Sección de entrada de confirmación de nueva contraseña con toggle-->
            <?php
            $id='password_confirmacion';
            $name='password_confirmacion';
            $label='Confirmar nueva contraseña';

            require ROOT_PATH.'/views/components/password-input.php';
            ?>

            <button type="submit" class="btn btn-primary">Actualizar contrasena</button>
        </form>
    </div>
</div>
