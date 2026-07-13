<div class="container">
    <div class="page-head"><div><div class="eyebrow">Administracion</div><h1>Configuracion del Sistema</h1></div></div>

    <?php require __DIR__ . '/../layout/_alerts.php'; ?>

    <div class="grid-2">
        <?php foreach ($configuraciones as $c): ?>
            <div class="card">
                <form method="POST" action="/configuracion/actualizar">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                    <input type="hidden" name="clave" value="<?= htmlspecialchars($c['clave']) ?>">
                    <label><?= htmlspecialchars($c['clave']) ?></label>
                    <p class="field-hint"><?= htmlspecialchars($c['descripcion'] ?? '') ?></p>
                    <div class="field" style="display:flex;gap:.5rem;">
                        <input type="text" name="valor" value="<?= htmlspecialchars($c['valor']) ?>">
                        <button type="submit" class="btn btn-primary btn-sm">Guardar</button>
                    </div>
                </form>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="card">
        <a class="btn btn-outline" href="/configuracion/mensajes">Ver mensajes de contacto recibidos</a>
    </div>
</div>
