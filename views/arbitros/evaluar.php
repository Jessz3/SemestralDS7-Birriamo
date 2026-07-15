<div class="container" style="max-width:560px;">
    <div class="page-head"><div><div class="eyebrow">Arbitraje</div><h1>Evaluar Desempeno del Arbitro</h1></div></div>

    <?php require __DIR__ . '/../layout/_alerts.php'; ?>

    <div class="card">
        <p><strong>Actividad:</strong> <?= htmlspecialchars($actividad['nombre']) ?></p>
        <p><strong>Arbitro:</strong> <?= htmlspecialchars($arbitro['nombre_completo']) ?></p>

        <form method="POST" action="<?= BASE_URL ?>/arbitros/evaluar">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
            <input type="hidden" name="actividad_id" value="<?= (int) $actividad['id'] ?>">
            <input type="hidden" name="arbitro_id" value="<?= (int) $arbitro['id'] ?>">

            <div class="field">
                <label>Organizador que evalua</label>
                <input type="text" value="<?= htmlspecialchars($organizador['nombre_completo']) ?>" readonly aria-readonly="true">
                <p class="field-hint">La evaluación quedará registrada a nombre del organizador que inició sesión.</p>
            </div>

            <?php
                $escala = fn(string $nombre, string $etiqueta, bool $requerido = false) => "
                <div class=\"field\">
                    <label>{$etiqueta}</label>
                    <select name=\"{$nombre}\" " . ($requerido ? 'required' : '') . ">
                        " . (!$requerido ? '<option value="">N/A</option>' : '') . "
                        <option value=\"5\">5 - Excelente</option>
                        <option value=\"4\">4 - Muy bueno</option>
                        <option value=\"3\">3 - Bueno</option>
                        <option value=\"2\">2 - Regular</option>
                        <option value=\"1\">1 - Deficiente</option>
                    </select>
                </div>";
            ?>
            <?= $escala('puntuacion', 'Puntuacion general', true) ?>
            <div class="grid-2">
                <?= $escala('puntualidad', 'Puntualidad') ?>
                <?= $escala('conocimiento_reglas', 'Conocimiento de reglas') ?>
            </div>
            <div class="grid-2">
                <?= $escala('imparcialidad', 'Imparcialidad') ?>
                <?= $escala('manejo_actividad', 'Manejo de la actividad') ?>
            </div>

            <div class="field">
                <label>Comentario (opcional)</label>
                <textarea name="comentario" rows="3"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Registrar evaluacion</button>
            <a class="btn btn-outline" href="<?= BASE_URL ?>/actividades/ver?id=<?= (int) $actividad['id'] ?>">Cancelar</a>
        </form>
    </div>
</div>
