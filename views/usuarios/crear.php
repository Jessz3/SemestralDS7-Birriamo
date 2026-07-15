<script src="<?= BASE_URL ?>/assets/js/password-toggle.js"></script>

<div class="container" style="max-width:640px;">
    <div class="page-head">
        <div>
            <div class="eyebrow"><?= htmlspecialchars($eyebrow) ?></div>
            <h1><?= htmlspecialchars($titulo) ?></h1>
        </div>
    </div>

    <?php require __DIR__ . '/../layout/_alerts.php'; ?>

    <div class="card">
        <form method="POST" action="<?= BASE_URL . htmlspecialchars($accion) ?>">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">

            <div class="grid-2">
                <div class="field">
                    <label>Nombre</label>
                    <input type="text" name="nombre" value="<?= htmlspecialchars($datos['nombre'] ?? '') ?>" minlength="2" maxlength="80" required>
                </div>
                <div class="field">
                    <label>Apellido</label>
                    <input type="text" name="apellido" value="<?= htmlspecialchars($datos['apellido'] ?? '') ?>" minlength="2" maxlength="80" required>
                </div>
            </div>

            <div class="grid-2">
                <div class="field">
                    <label>Nombre de usuario</label>
                    <input type="text" name="usuario" value="<?= htmlspecialchars($datos['usuario'] ?? '') ?>" pattern="[A-Za-z0-9]+" minlength="3" maxlength="60" title="Use solamente letras y numeros" required>
                </div>
                <div class="field">
                    <label>Rol</label>
                    <select name="rol" required>
                        <?php $rolSeleccionado = $datos['rol'] ?? $rolesPermitidos[0]; ?>
                        <?php foreach ($rolesPermitidos as $rol): ?>
                            <option value="<?= htmlspecialchars($rol) ?>" <?= $rolSeleccionado === $rol ? 'selected' : '' ?>>
                                <?= htmlspecialchars(ucfirst(strtolower($rol))) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (array_intersect($rolesPermitidos, ['ORGANIZADOR', 'PARTICIPANTE'])): ?>
                        <p class="field-hint">Organizador y Participante reciben tambien su perfil funcional automaticamente.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="field">
                <label>Correo electronico</label>
                <input type="email" name="correo" value="<?= htmlspecialchars($datos['correo'] ?? '') ?>" maxlength="150" required>
            </div>

            <div class="grid-2">
                <!--Sección de entrada de contraseña con toggle-->
                <?php
                $id='password';
                $name='password';
                $label='Contraseña';

                $hint='Entre 8 y 12 caracteres, con mayúscula y número.';

                require ROOT_PATH.'/views/components/password-input.php';
                ?>

                <!--Sección de entrada de frase de seguridad con toggle-->
                <?php
                $id='passphrase_llave';
                $name='passphrase_llave';
                $label='Frase de seguridad de la llave privada';

                $hint='Protege la llave RSA privada del usuario (maximo 12 caracteres). Solo el usuario debe conocerla.';

                require ROOT_PATH.'/views/components/password-input.php';
                ?>
            </div>

            <button type="submit" class="btn btn-primary"><?= htmlspecialchars($textoBoton) ?></button>
            <a class="btn btn-outline" href="<?= BASE_URL . htmlspecialchars($cancelar) ?>">Cancelar</a>
        </form>
    </div>
</div>

