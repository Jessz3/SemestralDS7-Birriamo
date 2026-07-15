<div class="container" style="max-width:560px;">
    <div class="page-head"><div><div class="eyebrow">Actividad</div><h1>Reportar Incidente</h1></div></div>

    <div class="card">
        <form method="POST" action="/actividades/incidente/crear">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
            <input type="hidden" name="actividad_id" value="<?= (int) $actividadId ?>">

            <div class="grid-2">
                <div class="field">
                    <label>Tipo de incidente</label>
                    <select name="tipo" required>
                        <?php foreach ($tipos as $t): ?>
                            <option value="<?= $t ?>"><?= ucfirst(strtolower(str_replace('_', ' ', $t))) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field">
                    <label>Gravedad</label>
                    <select name="gravedad" required>
                        <?php foreach ($gravedades as $g): ?>
                            <option value="<?= $g ?>"><?= ucfirst(strtolower($g)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="field">
                <label>Fecha y hora del incidente</label>
                <input type="datetime-local" name="fecha_incidente">
            </div>
            <div class="field">
                <label>Descripcion</label>
                <textarea name="descripcion" rows="4" minlength="10" maxlength="3000" required></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Registrar incidente</button>
            <a class="btn btn-outline" href="/actividades/ver?id=<?= (int) $actividadId ?>">Cancelar</a>
        </form>
    </div>
</div>
