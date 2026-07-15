<div class="container" style="max-width:560px;">
    <div class="page-head"><div><div class="eyebrow">Catalogo</div><h1>Editar Deporte</h1></div></div>

    <?php require __DIR__ . '/../layout/_alerts.php'; ?>

    <div class="card">
        <form method="POST" action="/deportes/editar">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
            <input type="hidden" name="id" value="<?= (int) $deporte['id'] ?>">

            <div class="field">
                <label>Nombre del deporte</label>
                <input type="text" name="nombre" value="<?= htmlspecialchars($deporte['nombre']) ?>" required>
            </div>
            <div class="field">
                <label>Descripcion</label>
                <textarea name="descripcion" rows="3"><?= htmlspecialchars($deporte['descripcion'] ?? '') ?></textarea>
            </div>
            <div class="field">
                <label><input type="checkbox" name="es_equipo" value="1" <?= (int) $deporte['es_equipo'] === 1 ? 'checked' : '' ?> style="width:auto;display:inline-block;"> Es un deporte de equipo</label>
            </div>
            <div class="grid-2">
                <div class="field">
                    <label>Minimo de jugadores</label>
                    <input type="number" name="minimo_jugadores" min="1" max="1000" step="1" value="<?= htmlspecialchars($deporte['minimo_jugadores'] ?? '') ?>">
                </div>
                <div class="field">
                    <label>Maximo de jugadores</label>
                    <input type="number" name="maximo_jugadores" min="1" max="1000" step="1" value="<?= htmlspecialchars($deporte['maximo_jugadores'] ?? '') ?>">
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Actualizar</button>
            <a class="btn btn-outline" href="/deportes">Cancelar</a>
        </form>
    </div>
</div>
