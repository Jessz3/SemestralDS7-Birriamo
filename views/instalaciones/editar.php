<div class="container" style="max-width:640px;">
    <div class="page-head"><div><div class="eyebrow">Catalogo</div><h1>Editar Instalacion</h1></div></div>

    <?php require __DIR__ . '/../layout/_alerts.php'; ?>

    <div class="card">
        <form method="POST" action="/instalaciones/editar">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
            <input type="hidden" name="id" value="<?= (int) $instalacion['id'] ?>">

            <div class="grid-2">
                <div class="field">
                    <label>Nombre</label>
                    <input type="text" name="nombre" value="<?= htmlspecialchars($instalacion['nombre']) ?>" required>
                </div>
                <div class="field">
                    <label>Tipo</label>
                    <select name="tipo" required>
                        <?php foreach ($tipos as $t): ?>
                            <option value="<?= $t ?>" <?= $instalacion['tipo'] === $t ? 'selected' : '' ?>><?= ucfirst(strtolower($t)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="field">
                <label>Descripcion</label>
                <textarea name="descripcion" rows="2"><?= htmlspecialchars($instalacion['descripcion'] ?? '') ?></textarea>
            </div>

            <div class="field">
                <label>Direccion</label>
                <input type="text" name="direccion" value="<?= htmlspecialchars($instalacion['direccion']) ?>" required>
            </div>

            <div class="grid-3">
                <div class="field">
                    <label>Provincia</label>
                    <input type="text" name="provincia" value="<?= htmlspecialchars($instalacion['provincia']) ?>">
                </div>
                <div class="field">
                    <label>Distrito</label>
                    <input type="text" name="distrito" value="<?= htmlspecialchars($instalacion['distrito'] ?? '') ?>">
                </div>
                <div class="field">
                    <label>Corregimiento</label>
                    <input type="text" name="corregimiento" value="<?= htmlspecialchars($instalacion['corregimiento'] ?? '') ?>">
                </div>
            </div>

            <div class="field">
                <label>Espacio disponible</label>
                <input type="text" name="espacio_disponible" value="<?= htmlspecialchars($instalacion['espacio_disponible'] ?? '') ?>">
            </div>

            <div class="grid-2">
                <div class="field">
                    <label>Capacidad de invitados</label>
                    <input type="number" name="capacidad_invitados" value="<?= (int) $instalacion['capacidad_invitados'] ?>" min="0" required>
                </div>
                <div class="field">
                    <label>Costo base ($)</label>
                    <input type="number" step="0.01" name="costo_base" value="<?= htmlspecialchars($instalacion['costo_base']) ?>" min="0" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Actualizar</button>
            <a class="btn btn-outline" href="/instalaciones">Cancelar</a>
        </form>
    </div>
</div>
