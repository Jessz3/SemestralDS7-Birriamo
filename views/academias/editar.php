<div class="container" style="max-width:600px;">
    <div class="page-head"><div><div class="eyebrow">Catalogo</div><h1>Editar Academia</h1></div></div>

    <?php require __DIR__ . '/../layout/_alerts.php'; ?>

    <div class="card">
        <form method="POST" action="/academias/editar">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
            <input type="hidden" name="id" value="<?= (int) $academia['id'] ?>">

            <div class="field">
                <label>Nombre de la academia</label>
                <input type="text" name="nombre" value="<?= htmlspecialchars($academia['nombre']) ?>" required>
            </div>
            <div class="grid-2">
                <div class="field">
                    <label>RUC (opcional)</label>
                    <input type="text" name="ruc" value="<?= htmlspecialchars($academia['ruc'] ?? '') ?>">
                </div>
                <div class="field">
                    <label>Telefono</label>
                    <input type="text" name="telefono" value="<?= htmlspecialchars($academia['telefono'] ?? '') ?>">
                </div>
            </div>
            <div class="grid-2">
                <div class="field">
                    <label>Correo electronico</label>
                    <input type="email" name="correo" value="<?= htmlspecialchars($academia['correo'] ?? '') ?>">
                </div>
                <div class="field">
                    <label>Direccion</label>
                    <input type="text" name="direccion" value="<?= htmlspecialchars($academia['direccion'] ?? '') ?>">
                </div>
            </div>
            <div class="field">
                <label>Descripcion</label>
                <textarea name="descripcion" rows="2"><?= htmlspecialchars($academia['descripcion'] ?? '') ?></textarea>
            </div>

            <div class="field">
                <label>Deportes que ofrece</label>
                <div class="grid-3">
                    <?php foreach ($deportes as $d): ?>
                        <label style="font-weight:400;">
                            <input type="checkbox" name="deportes[]" value="<?= (int) $d['id'] ?>"
                                <?= in_array((int) $d['id'], $deportesSeleccionados, true) ? 'checked' : '' ?>
                                style="width:auto;display:inline-block;"> <?= htmlspecialchars($d['nombre']) ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Actualizar</button>
            <a class="btn btn-outline" href="/academias">Cancelar</a>
        </form>
    </div>
</div>
