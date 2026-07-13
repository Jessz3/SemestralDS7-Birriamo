<div class="container" style="max-width:600px;">
    <div class="page-head"><div><div class="eyebrow">Catalogo</div><h1>Editar Organizador</h1></div></div>

    <?php require __DIR__ . '/../layout/_alerts.php'; ?>

    <div class="card">
        <p><strong><?= htmlspecialchars($organizador['nombre_completo']) ?></strong> — <?= htmlspecialchars($organizador['correo']) ?></p>
        <form method="POST" action="/organizadores/editar">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
            <input type="hidden" name="id" value="<?= (int) $organizador['id'] ?>">

            <div class="grid-2">
                <div class="field">
                    <label>Tipo de organizador</label>
                    <select name="tipo_organizador" required>
                        <?php foreach ($tipos as $t): ?>
                            <option value="<?= $t ?>" <?= $organizador['tipo_organizador'] === $t ? 'selected' : '' ?>><?= ucfirst(strtolower($t)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field">
                    <label>Academia (opcional)</label>
                    <select name="academia_id">
                        <option value="">Independiente</option>
                        <?php foreach ($academias as $a): ?>
                            <option value="<?= (int) $a['id'] ?>" <?= (int) $organizador['academia_id'] === (int) $a['id'] ? 'selected' : '' ?>><?= htmlspecialchars($a['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="field">
                <label>Nombre comercial (opcional)</label>
                <input type="text" name="nombre_comercial" value="<?= htmlspecialchars($organizador['nombre_comercial'] ?? '') ?>">
            </div>
            <div class="field">
                <label>Descripcion</label>
                <textarea name="descripcion" rows="2"><?= htmlspecialchars($organizador['descripcion'] ?? '') ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Actualizar</button>
            <a class="btn btn-outline" href="/organizadores">Cancelar</a>
        </form>
    </div>
</div>
