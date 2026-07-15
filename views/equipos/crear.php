<div class="container" style="max-width:600px;">
    <div class="page-head"><div><div class="eyebrow">Equipos</div><h1>Nuevo Equipo</h1></div></div>

    <?php require __DIR__ . '/../layout/_alerts.php'; ?>

    <div class="card">
        <form method="POST" action="/equipos/crear" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">

            <div class="field">
                <label>Nombre del equipo</label>
                <input type="text" name="nombre" value="<?= htmlspecialchars($datos['nombre'] ?? '') ?>" minlength="2" maxlength="120" required>
            </div>

            <div class="grid-2">
                <div class="field">
                    <label>Deporte</label>
                    <select name="deporte_id" required>
                        <?php foreach ($deportes as $d): ?>
                            <option value="<?= (int) $d['id'] ?>"><?= htmlspecialchars($d['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field">
                    <label>Academia (opcional)</label>
                    <select name="academia_id">
                        <option value="">Equipo independiente</option>
                        <?php foreach ($academias as $a): ?>
                            <option value="<?= (int) $a['id'] ?>"><?= htmlspecialchars($a['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="field">
                <label>Avatar del equipo</label>
                <input type="file" name="avatar" accept="image/png,image/jpeg,image/webp">
                <p class="field-hint">JPG, PNG o WEBP, maximo 2MB.</p>
            </div>

            <div class="field">
                <label>Descripcion (opcional)</label>
                <textarea name="descripcion" rows="2"><?= htmlspecialchars($datos['descripcion'] ?? '') ?></textarea>
            </div>

            <?php if (!$esParticipante): ?>
            <hr style="border:none;border-top:1px solid var(--color-border);margin:1.2rem 0;">
            <p class="field-hint">Datos del representante (se creara automaticamente su cuenta de Participante si no existe):</p>

            <div class="grid-2">
                <div class="field">
                    <label>Nombre del representante</label>
                    <input type="text" name="rep_nombre" value="<?= htmlspecialchars($datos['rep_nombre'] ?? '') ?>" minlength="2" maxlength="80" required>
                </div>
                <div class="field">
                    <label>Apellido del representante</label>
                    <input type="text" name="rep_apellido" value="<?= htmlspecialchars($datos['rep_apellido'] ?? '') ?>" minlength="2" maxlength="80" required>
                </div>
            </div>
            <div class="grid-2">
                <div class="field">
                    <label>Correo del representante</label>
                    <input type="email" name="rep_correo" value="<?= htmlspecialchars($datos['rep_correo'] ?? '') ?>" maxlength="150" required>
                </div>
                <div class="field">
                    <label>Telefono del representante</label>
                    <input type="tel" name="rep_telefono" value="<?= htmlspecialchars($datos['rep_telefono'] ?? '') ?>" pattern="6[0-9]{7}" minlength="8" maxlength="8" inputmode="numeric" placeholder="61234567" title="Ingrese un celular panameno de 8 digitos que comience con 6">
                </div>
            </div>
            <?php else: ?>
                <p class="field-hint">Quedarás registrado automáticamente como representante de este equipo.</p>
            <?php endif; ?>

            <button type="submit" class="btn btn-primary">Guardar equipo</button>
            <a class="btn btn-outline" href="/equipos">Cancelar</a>
        </form>
    </div>
</div>
