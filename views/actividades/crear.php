<div class="container" style="max-width:780px;">
    <div class="page-head"><div><div class="eyebrow">Actividades</div><h1>Nueva Actividad Deportiva</h1></div></div>

    <?php require __DIR__ . '/../layout/_alerts.php'; ?>

    <div class="card">
        <p class="field-hint">La actividad se creara en estado <strong>BORRADOR</strong>. Publicala luego para que sea visible al publico y reciba inscripciones.</p>
        <form method="POST" action="/actividades/crear">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">

            <div class="field">
                <label>Nombre de la actividad</label>
                <input type="text" name="nombre" value="<?= htmlspecialchars($datos['nombre'] ?? '') ?>" minlength="3" maxlength="180" required>
            </div>

            <div class="grid-2">
                <div class="field">
                    <label>Tipo</label>
                    <select name="tipo" required>
                        <option value="TORNEO">Torneo</option>
                        <option value="ENTRENAMIENTO">Entrenamiento</option>
                        <option value="BIRRIA">Birria</option>
                        <option value="EVENTO">Evento</option>
                    </select>
                </div>
                <div class="field">
                    <label>Modalidad</label>
                    <select name="modalidad" required>
                        <option value="EQUIPO">Por equipo</option>
                        <option value="INDIVIDUAL">Individual</option>
                        <option value="MIXTA">Mixta (equipo e individual)</option>
                    </select>
                </div>
            </div>

            <div class="grid-2">
                <div class="field">
                    <label>Organizador</label>
                    <select name="organizador_id" required>
                        <?php foreach ($organizadores as $o): ?>
                            <option value="<?= (int) $o['id'] ?>"><?= htmlspecialchars($o['nombre_completo']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field">
                    <label>Deporte</label>
                    <select name="deporte_id" required>
                        <?php foreach ($deportes as $d): ?>
                            <option value="<?= (int) $d['id'] ?>"><?= htmlspecialchars($d['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="grid-2">
                <div class="field">
                    <label>Instalacion</label>
                    <select name="instalacion_id" required>
                        <?php foreach ($instalaciones as $i): ?>
                            <option value="<?= (int) $i['id'] ?>"><?= htmlspecialchars($i['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field">
                    <label>Entrenador (opcional)</label>
                    <select name="entrenador_id">
                        <option value="">Sin asignar</option>
                        <?php foreach ($entrenadores as $e): ?>
                            <option value="<?= (int) $e['id'] ?>"><?= htmlspecialchars($e['nombre_completo']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="grid-2">
                <div class="field">
                    <label>Fecha y hora de inicio</label>
                    <input type="datetime-local" name="fecha_inicio" required>
                </div>
                <div class="field">
                    <label>Fecha y hora de fin</label>
                    <input type="datetime-local" name="fecha_fin" required>
                </div>
            </div>
            <div class="field">
                <label>Cierre de inscripciones (opcional)</label>
                <input type="datetime-local" name="fecha_cierre_inscripcion">
            </div>

            <div class="grid-2">
                <div class="field">
                    <label>Edad minima (opcional)</label>
                    <input type="number" name="edad_minima" min="0" max="120" step="1">
                </div>
                <div class="field">
                    <label>Edad maxima (opcional)</label>
                    <input type="number" name="edad_maxima" min="0" max="120" step="1">
                </div>
            </div>

            <div class="grid-2">
                <div class="field">
                    <label>Cupos disponibles</label>
                    <input type="number" name="cupos_disponibles" value="20" min="1" max="65535" step="1" required>
                </div>
                <div class="field">
                    <label>Capacidad de invitados</label>
                    <input type="number" name="capacidad_invitados" value="0" min="0" max="65535" step="1">
                </div>
            </div>

            <div class="field">
                <label style="font-weight:400;"><input type="checkbox" name="requiere_pago" value="1" checked style="width:auto;display:inline-block;"> Esta actividad requiere pago</label>
            </div>
            <div class="grid-2">
                <div class="field">
                    <label>Costo de inscripcion ($)</label>
                    <input type="number" step="0.01" name="costo_inscripcion" value="0" min="0" max="99999999.99" required>
                </div>
                <div class="field">
                    <label>Costo de instalacion ($, uso interno)</label>
                    <input type="number" step="0.01" name="costo_instalacion" value="0" min="0" max="99999999.99">
                </div>
            </div>

            <div class="field">
                <label>Descripcion</label>
                <textarea name="descripcion" rows="3" minlength="10" maxlength="5000" required><?= htmlspecialchars($datos['descripcion'] ?? '') ?></textarea>
            </div>
            <div class="field">
                <label>Reglas (opcional)</label>
                <textarea name="reglas" rows="3"><?= htmlspecialchars($datos['reglas'] ?? '') ?></textarea>
            </div>

            <div class="field">
                <label>Arbitros asignados (opcional)</label>
                <div class="grid-3">
                    <?php foreach ($arbitros as $ar): ?>
                        <label style="font-weight:400;"><input type="checkbox" name="arbitros[]" value="<?= (int) $ar['id'] ?>" style="width:auto;display:inline-block;"> <?= htmlspecialchars($ar['nombre_completo']) ?></label>
                    <?php endforeach; ?>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Guardar actividad</button>
            <a class="btn btn-outline" href="/actividades">Cancelar</a>
        </form>
    </div>
</div>
