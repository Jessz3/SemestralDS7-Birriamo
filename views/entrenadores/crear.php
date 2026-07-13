<div class="container" style="max-width:600px;">
    <div class="page-head"><div><div class="eyebrow">Catalogo</div><h1>Nuevo Entrenador</h1></div></div>

    <?php require __DIR__ . '/../layout/_alerts.php'; ?>

    <div class="card">
        <form method="POST" action="/entrenadores/crear">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">

            <div class="field">
                <label>Nombre completo</label>
                <input type="text" name="nombre_completo" required>
            </div>
            <div class="grid-2">
                <div class="field">
                    <label>Cedula (opcional)</label>
                    <input type="text" name="cedula">
                </div>
                <div class="field">
                    <label>Anos de experiencia</label>
                    <input type="number" name="anios_experiencia" min="0">
                </div>
            </div>
            <div class="grid-2">
                <div class="field">
                    <label>Correo</label>
                    <input type="email" name="correo">
                </div>
                <div class="field">
                    <label>Telefono</label>
                    <input type="text" name="telefono">
                </div>
            </div>
            <div class="grid-2">
                <div class="field">
                    <label>Academia (opcional)</label>
                    <select name="academia_id">
                        <option value="">Ninguna</option>
                        <?php foreach ($academias as $a): ?>
                            <option value="<?= (int) $a['id'] ?>"><?= htmlspecialchars($a['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field">
                    <label>Organizador (opcional)</label>
                    <select name="organizador_id">
                        <option value="">Ninguno</option>
                        <?php foreach ($organizadores as $o): ?>
                            <option value="<?= (int) $o['id'] ?>"><?= htmlspecialchars($o['nombre_completo']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="field">
                <label>Certificaciones</label>
                <textarea name="certificaciones" rows="2"></textarea>
            </div>

            <div class="field">
                <label>Deportes que entrena</label>
                <div class="grid-3">
                    <?php foreach ($deportes as $d): ?>
                        <label style="font-weight:400;"><input type="checkbox" name="deportes[]" value="<?= (int) $d['id'] ?>" style="width:auto;display:inline-block;"> <?= htmlspecialchars($d['nombre']) ?></label>
                    <?php endforeach; ?>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Guardar</button>
            <a class="btn btn-outline" href="/entrenadores">Cancelar</a>
        </form>
    </div>
</div>
