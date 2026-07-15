<div class="container" style="max-width:780px;">
    <div class="page-head"><div><div class="eyebrow">Actividades</div><h1>Editar Actividad</h1></div></div>

    <?php require __DIR__ . '/../layout/_alerts.php'; ?>

    <div class="card">
        <form method="POST" action="/actividades/editar">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
            <input type="hidden" name="id" value="<?= (int) $actividad['id'] ?>">

            <div class="field">
                <label>Nombre de la actividad</label>
                <input type="text" name="nombre" value="<?= htmlspecialchars($actividad['nombre']) ?>" minlength="3" maxlength="180" required>
            </div>

            <div class="grid-2">
                <div class="field">
                    <label>Tipo</label>
                    <select name="tipo" required>
                        <?php foreach (['TORNEO','ENTRENAMIENTO','BIRRIA','EVENTO'] as $t): ?>
                            <option value="<?= $t ?>" <?= $actividad['tipo'] === $t ? 'selected' : '' ?>><?= ucfirst(strtolower($t)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field">
                    <label>Modalidad</label>
                    <select name="modalidad" required>
                        <?php foreach (['EQUIPO' => 'Por equipo', 'INDIVIDUAL' => 'Individual', 'MIXTA' => 'Mixta'] as $val => $label): ?>
                            <option value="<?= $val ?>" <?= $actividad['modalidad'] === $val ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="grid-2">
                <div class="field">
                    <label>Deporte</label>
                    <select name="deporte_id" required>
                        <?php foreach ($deportes as $d): ?>
                            <option value="<?= (int) $d['id'] ?>" <?= (int) $actividad['deporte_id'] === (int) $d['id'] ? 'selected' : '' ?>><?= htmlspecialchars($d['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field">
                    <label>Instalacion</label>
                    <select name="instalacion_id" required>
                        <?php foreach ($instalaciones as $i): ?>
                            <option value="<?= (int) $i['id'] ?>" <?= (int) $actividad['instalacion_id'] === (int) $i['id'] ? 'selected' : '' ?>><?= htmlspecialchars($i['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="field">
                <label>Entrenador (opcional)</label>
                <select name="entrenador_id">
                    <option value="">Sin asignar</option>
                    <?php foreach ($entrenadores as $e): ?>
                        <option value="<?= (int) $e['id'] ?>" <?= (int) ($actividad['entrenador_id'] ?? 0) === (int) $e['id'] ? 'selected' : '' ?>><?= htmlspecialchars($e['nombre_completo']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grid-2">
                <div class="field">
                    <label>Fecha y hora de inicio</label>
                    <input type="datetime-local" name="fecha_inicio" value="<?= str_replace(' ', 'T', substr($actividad['fecha_inicio'], 0, 16)) ?>" required>
                </div>
                <div class="field">
                    <label>Fecha y hora de fin</label>
                    <input type="datetime-local" name="fecha_fin" value="<?= str_replace(' ', 'T', substr($actividad['fecha_fin'], 0, 16)) ?>" required>
                </div>
            </div>

            <div class="grid-2">
                <div class="field">
                    <label>Edad minima (opcional)</label>
                    <input type="number" name="edad_minima" min="0" max="120" step="1" value="<?= htmlspecialchars($actividad['edad_minima'] ?? '') ?>">
                </div>
                <div class="field">
                    <label>Edad maxima (opcional)</label>
                    <input type="number" name="edad_maxima" min="0" max="120" step="1" value="<?= htmlspecialchars($actividad['edad_maxima'] ?? '') ?>">
                </div>
            </div>

            <div class="grid-2">
                <div class="field">
                    <label>Cupos disponibles</label>
                    <input type="number" name="cupos_disponibles" value="<?= (int) $actividad['cupos_disponibles'] ?>" min="1" max="65535" step="1" required>
                </div>
                <div class="field">
                    <label>Capacidad de invitados</label>
                    <input type="number" name="capacidad_invitados" value="<?= (int) $actividad['capacidad_invitados'] ?>" min="0" max="65535" step="1">
                </div>
            </div>

            <div class="field">
                <label style="font-weight:400;"><input type="checkbox" name="requiere_pago" value="1" <?= (int) $actividad['requiere_pago'] === 1 ? 'checked' : '' ?> style="width:auto;display:inline-block;"> Esta actividad requiere pago</label>
            </div>
            <div class="grid-2">
                <div class="field">
                    <label>Costo de inscripcion ($)</label>
                    <input type="number" step="0.01" name="costo_inscripcion" value="<?= htmlspecialchars($actividad['costo_inscripcion']) ?>" min="0" max="99999999.99" required>
                </div>
                <div class="field">
                    <label>Costo de instalacion ($, uso interno)</label>
                    <input type="number" step="0.01" name="costo_instalacion" value="<?= htmlspecialchars($actividad['costo_instalacion']) ?>" min="0" max="99999999.99">
                </div>
            </div>

            <div class="field">
                <label>Descripcion</label>
                <textarea name="descripcion" rows="3" minlength="10" maxlength="5000" required><?= htmlspecialchars($actividad['descripcion']) ?></textarea>
            </div>
            <div class="field">
                <label>Reglas (opcional)</label>
                <textarea name="reglas" rows="3"><?= htmlspecialchars($actividad['reglas'] ?? '') ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Actualizar actividad</button>
            <a class="btn btn-outline" href="/actividades/ver?id=<?= (int) $actividad['id'] ?>">Cancelar</a>
        </form>
    </div>
</div>
