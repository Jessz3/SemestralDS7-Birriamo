<div class="container" style="max-width:600px;">
    <div class="page-head"><div><div class="eyebrow">Catalogo</div><h1>Nueva Academia</h1></div></div>

    <?php require __DIR__ . '/../layout/_alerts.php'; ?>

    <div class="card">
        <form method="POST" action="/academias/crear">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">

            <div class="field">
                <label>Nombre de la academia</label>
                <input type="text" name="nombre" value="<?= htmlspecialchars($datos['nombre'] ?? '') ?>" minlength="2" maxlength="150" required>
            </div>
            <div class="grid-2">
                <div class="field">
                    <label>RUC (opcional)</label>
                    <input type="text" name="ruc" value="<?= htmlspecialchars($datos['ruc'] ?? '') ?>" maxlength="30">
                </div>
                <div class="field">
                    <label>Telefono</label>
                    <input type="tel" name="telefono" value="<?= htmlspecialchars($datos['telefono'] ?? '') ?>" pattern="(?:6[0-9]{7}|[2-9][0-9]{6})" maxlength="8" inputmode="numeric" placeholder="61234567" title="Ingrese un celular de 8 digitos que inicie en 6 o un telefono fijo panameno de 7 digitos">
                </div>
            </div>
            <div class="grid-2">
                <div class="field">
                    <label>Correo electronico</label>
                    <input type="email" name="correo" value="<?= htmlspecialchars($datos['correo'] ?? '') ?>" maxlength="150">
                </div>
                <div class="field">
                    <label>Direccion</label>
                    <input type="text" name="direccion" value="<?= htmlspecialchars($datos['direccion'] ?? '') ?>" maxlength="255">
                </div>
            </div>
            <div class="field">
                <label>Descripcion</label>
                <textarea name="descripcion" rows="2"><?= htmlspecialchars($datos['descripcion'] ?? '') ?></textarea>
            </div>

            <div class="field">
                <label>Deportes que ofrece</label>
                <div class="grid-3">
                    <?php foreach ($deportes as $d): ?>
                        <label style="font-weight:400;"><input type="checkbox" name="deportes[]" value="<?= (int) $d['id'] ?>" style="width:auto;display:inline-block;"> <?= htmlspecialchars($d['nombre']) ?></label>
                    <?php endforeach; ?>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Guardar</button>
            <a class="btn btn-outline" href="/academias">Cancelar</a>
        </form>
    </div>
</div>
