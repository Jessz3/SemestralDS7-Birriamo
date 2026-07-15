<div class="container" style="max-width:600px;">
    <div class="page-head"><div><div class="eyebrow">Catalogo</div><h1>Nuevo Entrenador</h1></div></div>

    <?php require __DIR__ . '/../layout/_alerts.php'; ?>

    <div class="card">
        <form method="POST" action="<?= BASE_URL ?>/entrenadores/crear">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">

            <div class="field">
                <label>Nombre completo</label>
                <input type="text" name="nombre_completo" value="<?= htmlspecialchars($datos['nombre_completo'] ?? '') ?>" minlength="3" maxlength="160" required>
            </div>
            <div class="grid-2">
                <div class="field">
                    <label>Cedula (opcional)</label>
                    <input type="text" name="cedula" value="<?= htmlspecialchars($datos['cedula'] ?? '') ?>" maxlength="30">
                </div>
                <div class="field">
                    <label>Años de experiencia</label>
                    <input type="number" name="anios_experiencia" value="<?= htmlspecialchars((string) ($datos['anios_experiencia'] ?? '')) ?>" min="0" max="65535" step="1">
                </div>
            </div>
            <div class="grid-2">
                <div class="field">
                    <label>Correo</label>
                    <input type="email" name="correo" value="<?= htmlspecialchars($datos['correo'] ?? '') ?>" maxlength="150" required>
                </div>
                <div class="field">
                    <label>Telefono celular</label>
                    <input type="tel" name="telefono" value="<?= htmlspecialchars($datos['telefono'] ?? '') ?>" pattern="6[0-9]{7}" minlength="8" maxlength="8" inputmode="numeric" placeholder="61234567" title="Ingrese 8 digitos, comenzando con 6" required>
                </div>
            </div>
            <div class="grid-2">
                <div class="field">
                    <label>Academia (opcional)</label>
                    <select name="academia_id">
                        <option value="">Ninguna</option>
                        <?php foreach ($academias as $a): ?>
                            <option value="<?= (int) $a['id'] ?>" <?= (int) ($datos['academia_id'] ?? 0) === (int) $a['id'] ? 'selected' : '' ?>><?= htmlspecialchars($a['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field">
                    <label>Organizador (opcional)</label>
                    <select name="organizador_id">
                        <option value="">Ninguno</option>
                        <?php foreach ($organizadores as $o): ?>
                            <option value="<?= (int) $o['id'] ?>" <?= (int) ($datos['organizador_id'] ?? 0) === (int) $o['id'] ? 'selected' : '' ?>><?= htmlspecialchars($o['nombre_completo']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="field">
                <label>Certificaciones</label>
                <textarea name="certificaciones" rows="2"><?= htmlspecialchars($datos['certificaciones'] ?? '') ?></textarea>
            </div>

            <div class="field">
                <label>Deportes que entrena</label>
                <div class="grid-3">
                    <?php foreach ($deportes as $d): ?>
                        <label style="font-weight:400;"><input type="checkbox" name="deportes[]" value="<?= (int) $d['id'] ?>" <?= in_array((int) $d['id'], $datos['deportes'] ?? [], true) ? 'checked' : '' ?> style="width:auto;display:inline-block;"> <?= htmlspecialchars($d['nombre']) ?></label>
                    <?php endforeach; ?>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Guardar</button>
            <a class="btn btn-outline" href="<?= BASE_URL ?>/entrenadores">Cancelar</a>
        </form>
    </div>
</div>
