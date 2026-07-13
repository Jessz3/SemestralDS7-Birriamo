<div class="container" style="max-width:640px;">
    <div class="page-head">
        <div>
            <div class="eyebrow">Administracion</div>
            <h1>Nuevo Usuario</h1>
        </div>
    </div>

    <?php require __DIR__ . '/../layout/_alerts.php'; ?>

    <div class="card">
        <form method="POST" action="/usuarios/crear">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">

            <div class="grid-2">
                <div class="field">
                    <label>Nombre</label>
                    <input type="text" name="nombre" value="<?= htmlspecialchars($datos['nombre'] ?? '') ?>" required>
                </div>
                <div class="field">
                    <label>Apellido</label>
                    <input type="text" name="apellido" value="<?= htmlspecialchars($datos['apellido'] ?? '') ?>" required>
                </div>
            </div>

            <div class="grid-2">
                <div class="field">
                    <label>Nombre de usuario</label>
                    <input type="text" name="usuario" value="<?= htmlspecialchars($datos['usuario'] ?? '') ?>" required>
                </div>
                <div class="field">
                    <label>Rol</label>
                    <select name="rol" required>
                        <option value="OPERADOR">Operador</option>
                        <option value="ADMINISTRADOR">Administrador</option>
                    </select>
                </div>
            </div>

            <div class="field">
                <label>Correo electronico</label>
                <input type="email" name="correo" value="<?= htmlspecialchars($datos['correo'] ?? '') ?>" required>
            </div>

            <div class="grid-2">
                <div class="field">
                    <label>Contrasena</label>
                    <input type="password" name="password" required>
                    <p class="field-hint">Minimo 8 caracteres, con mayuscula y numero.</p>
                </div>
                <div class="field">
                    <label>Frase de seguridad de llave privada</label>
                    <input type="password" name="passphrase_llave" required>
                    <p class="field-hint">Protege la llave RSA privada del usuario (no repudio). Solo el usuario debe conocerla.</p>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Guardar usuario</button>
            <a class="btn btn-outline" href="/usuarios">Cancelar</a>
        </form>
    </div>
</div>
