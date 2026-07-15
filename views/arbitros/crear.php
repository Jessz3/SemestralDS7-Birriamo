<div class="container" style="max-width:560px;">
    <div class="page-head"><div><div class="eyebrow">Arbitraje</div><h1>Nuevo Arbitro</h1></div></div>

    <?php require __DIR__ . '/../layout/_alerts.php'; ?>

    <div class="card">
        <form method="POST" action="<?= BASE_URL ?>/arbitros/crear">
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
                    <label>Licencia</label>
                    <input type="text" name="licencia">
                </div>
            </div>
            <div class="grid-2">
                <div class="field">
                    <label>Correo</label>
                    <input type="email" name="correo">
                </div>
                <div class="field">
                    <label>Telefono celular</label>
                    <input type="tel" name="telefono" pattern="6[0-9]{7}" maxlength="8" inputmode="numeric" placeholder="61234567" title="Ingrese 8 digitos, comenzando con 6" required>
                </div>
            </div>
            <div class="field">
                <label>Experiencia</label>
                <textarea name="experiencia" rows="2"></textarea>
            </div>

            <div class="field">
                <label>Deportes que arbitra</label>
                <div class="grid-3">
                    <?php foreach ($deportes as $d): ?>
                        <label style="font-weight:400;"><input type="checkbox" name="deportes[]" value="<?= (int) $d['id'] ?>" style="width:auto;display:inline-block;"> <?= htmlspecialchars($d['nombre']) ?></label>
                    <?php endforeach; ?>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Guardar</button>
            <a class="btn btn-outline" href="<?= BASE_URL ?>/arbitros">Cancelar</a>
        </form>
    </div>
</div>
