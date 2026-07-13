<div class="container" style="max-width:600px;">
    <div class="page-head"><div><div class="eyebrow">Catalogo</div><h1>Nueva Academia</h1></div></div>

    <?php require __DIR__ . '/../layout/_alerts.php'; ?>

    <div class="card">
        <form method="POST" action="/academias/crear">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">

            <div class="field">
                <label>Nombre de la academia</label>
                <input type="text" name="nombre" value="<?= htmlspecialchars($datos['nombre'] ?? '') ?>" required>
            </div>
            <div class="grid-2">
                <div class="field">
                    <label>RUC (opcional)</label>
                    <input type="text" name="ruc" value="<?= htmlspecialchars($datos['ruc'] ?? '') ?>">
                </div>
                <div class="field">
                    <label>Telefono</label>
                    <input type="text" name="telefono" value="<?= htmlspecialchars($datos['telefono'] ?? '') ?>">
                </div>
            </div>
            <div class="grid-2">
                <div class="field">
                    <label>Correo electronico</label>
                    <input type="email" name="correo" value="<?= htmlspecialchars($datos['correo'] ?? '') ?>">
                </div>
                <div class="field">
                    <label>Direccion</label>
                    <input type="text" name="direccion" value="<?= htmlspecialchars($datos['direccion'] ?? '') ?>">
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
