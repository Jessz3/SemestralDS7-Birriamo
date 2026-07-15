<div class="container" style="max-width:640px;">
    <div class="page-head"><div><div class="eyebrow">Catalogo</div><h1>Nueva Instalacion</h1></div></div>

    <?php require __DIR__ . '/../layout/_alerts.php'; ?>

    <div class="card">
        <form method="POST" action="/instalaciones/crear">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">

            <div class="grid-2">
                <div class="field">
                    <label>Nombre</label>
                    <input type="text" name="nombre" value="<?= htmlspecialchars($datos['nombre'] ?? '') ?>" required>
                </div>
                <div class="field">
                    <label>Tipo</label>
                    <select name="tipo" required>
                        <?php foreach ($tipos as $t): ?>
                            <option value="<?= $t ?>"><?= ucfirst(strtolower($t)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="field">
                <label>Descripcion</label>
                <textarea name="descripcion" rows="2"><?= htmlspecialchars($datos['descripcion'] ?? '') ?></textarea>
            </div>

            <div class="field">
                <label>Direccion</label>
                <input type="text" name="direccion" value="<?= htmlspecialchars($datos['direccion'] ?? '') ?>" required>
            </div>

            <div class="grid-3">
                <div class="field">
                    <label>Provincia</label>
                    <input type="text" name="provincia" value="<?= htmlspecialchars($datos['provincia'] ?? 'Panamá') ?>">
                </div>
                <div class="field">
                    <label>Distrito</label>
                    <input type="text" name="distrito" value="<?= htmlspecialchars($datos['distrito'] ?? '') ?>">
                </div>
                <div class="field">
                    <label>Corregimiento</label>
                    <input type="text" name="corregimiento" value="<?= htmlspecialchars($datos['corregimiento'] ?? '') ?>">
                </div>
            </div>

            <div class="field">
                <label>Espacio disponible</label>
                <input type="text" name="espacio_disponible" placeholder="Cancha techada, parqueo, gradas..." value="<?= htmlspecialchars($datos['espacio_disponible'] ?? '') ?>">
            </div>

            <div class="grid-2">
                <div class="field">
                    <label>Capacidad de invitados</label>
                    <input type="number" name="capacidad_invitados" value="<?= htmlspecialchars($datos['capacidad_invitados'] ?? 0) ?>" min="0" max="65535" step="1" required>
                </div>
                <div class="field">
                    <label>Costo base ($)</label>
                    <input type="number" step="0.01" name="costo_base" value="<?= htmlspecialchars($datos['costo_base'] ?? 0) ?>" min="0" max="99999999.99" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Guardar</button>
            <a class="btn btn-outline" href="/instalaciones">Cancelar</a>
        </form>
    </div>
</div>
