<div class="container" style="max-width:560px;">
    <div class="page-head"><div><div class="eyebrow">Catalogo</div><h1>Nuevo Deporte</h1></div></div>

    <?php require __DIR__ . '/../layout/_alerts.php'; ?>

    <div class="card">
        <form method="POST" action="/deportes/crear">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">

            <div class="field">
                <label>Nombre del deporte</label>
                <input type="text" name="nombre" value="<?= htmlspecialchars($datos['nombre'] ?? '') ?>" required>
            </div>
            <div class="field">
                <label>Descripcion</label>
                <textarea name="descripcion" rows="3"><?= htmlspecialchars($datos['descripcion'] ?? '') ?></textarea>
            </div>
            <div class="field">
                <label><input type="checkbox" name="es_equipo" value="1" style="width:auto;display:inline-block;"> Es un deporte de equipo</label>
            </div>
            <div class="grid-2">
                <div class="field">
                    <label>Minimo de jugadores</label>
                    <input type="number" name="minimo_jugadores" min="1" max="1000" step="1">
                </div>
                <div class="field">
                    <label>Maximo de jugadores</label>
                    <input type="number" name="maximo_jugadores" min="1" max="1000" step="1">
                </div>
            </div>
            <p class="field-hint">Deja los campos de jugadores en blanco para disciplinas individuales.</p>

            <button type="submit" class="btn btn-primary">Guardar</button>
            <a class="btn btn-outline" href="/deportes">Cancelar</a>
        </form>
    </div>
</div>
