<div class="container" style="max-width:640px;">
    <div class="page-head">
        <div>
            <div class="eyebrow">Administracion</div>
            <h1>Editar Usuario</h1>
        </div>
    </div>

    <?php require __DIR__ . '/../layout/_alerts.php'; ?>

    <div class="card">
        <form method="POST" action="/usuarios/editar">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
            <input type="hidden" name="id" value="<?= (int) $usuario['id'] ?>">

            <div class="grid-2">
                <div class="field">
                    <label>Nombre</label>
                    <input type="text" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
                </div>
                <div class="field">
                    <label>Apellido</label>
                    <input type="text" name="apellido" value="<?= htmlspecialchars($usuario['apellido']) ?>" required>
                </div>
            </div>

            <div class="grid-2">
                <div class="field">
                    <label>Nombre de usuario</label>
                    <input type="text" name="usuario" value="<?= htmlspecialchars($usuario['usuario']) ?>" required>
                </div>
                <div class="field">
                    <label>Rol</label>
                    <select name="rol" required>
                        <option value="OPERADOR" <?= $usuario['rol'] === 'OPERADOR' ? 'selected' : '' ?>>Operador</option>
                        <option value="ADMINISTRADOR" <?= $usuario['rol'] === 'ADMINISTRADOR' ? 'selected' : '' ?>>Administrador</option>
                    </select>
                </div>
            </div>

            <div class="field">
                <label>Correo electronico</label>
                <input type="email" name="correo" value="<?= htmlspecialchars($usuario['correo']) ?>" required>
            </div>

            <button type="submit" class="btn btn-primary">Actualizar usuario</button>
            <a class="btn btn-outline" href="/usuarios">Cancelar</a>
        </form>
    </div>
</div>
