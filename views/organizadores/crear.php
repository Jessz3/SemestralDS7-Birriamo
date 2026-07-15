<div class="container" style="max-width:600px;">
    <div class="page-head"><div><div class="eyebrow">Catalogo</div><h1>Nuevo Organizador</h1></div></div>

    <?php require __DIR__ . '/../layout/_alerts.php'; ?>

    <div class="card">
        <p class="field-hint">Se creara automaticamente una cuenta de usuario (rol ORGANIZADOR) con su propio par de llaves RSA para firma digital.</p>
        <form method="POST" action="/organizadores/crear">
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
                    <label>Correo electronico</label>
                    <input type="email" name="correo" value="<?= htmlspecialchars($datos['correo'] ?? '') ?>" maxlength="150" required>
                </div>
                <div class="field">
                    <label>Telefono</label>
                    <input type="tel" name="telefono" value="<?= htmlspecialchars($datos['telefono'] ?? '') ?>" pattern="6[0-9]{7}" minlength="8" maxlength="8" inputmode="numeric" placeholder="61234567" title="Ingrese un celular panameno de 8 digitos que comience con 6">
                </div>
            </div>

            <div class="grid-2">
                <div class="field">
                    <label>Tipo de organizador</label>
                    <select name="tipo_organizador" required>
                        <?php foreach ($tipos as $t): ?>
                            <option value="<?= $t ?>"><?= ucfirst(strtolower($t)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field">
                    <label>Academia (opcional)</label>
                    <select name="academia_id">
                        <option value="">Independiente</option>
                        <?php foreach ($academias as $a): ?>
                            <option value="<?= (int) $a['id'] ?>"><?= htmlspecialchars($a['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="field">
                <label>Nombre comercial (opcional)</label>
                <input type="text" name="nombre_comercial" value="<?= htmlspecialchars($datos['nombre_comercial'] ?? '') ?>" maxlength="160">
            </div>
            <div class="field">
                <label>Descripcion</label>
                <textarea name="descripcion" rows="2"><?= htmlspecialchars($datos['descripcion'] ?? '') ?></textarea>
            </div>

            <div class="field">
                <label>Frase de seguridad de llave privada</label>
                <input type="password" name="passphrase_llave" maxlength="12" required>
                <p class="field-hint">Protege la llave RSA privada del organizador. Solo el organizador debe conocerla. Maximo 12 caracteres.</p>
            </div>

            <button type="submit" class="btn btn-primary">Guardar</button>
            <a class="btn btn-outline" href="/organizadores">Cancelar</a>
        </form>
    </div>
</div>
