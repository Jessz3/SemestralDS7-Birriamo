<div class="section-public" style="max-width:520px;">
    <h1>Inscripcion Individual</h1>
    <p class="field-hint">
        <?= htmlspecialchars($actividad['nombre']) ?> ·
        <?= (int) $actividad['requiere_pago'] === 1 ? 'Costo: $' . number_format((float) $actividad['costo_inscripcion'], 2) : 'Gratuita' ?>
    </p>

    <?php require __DIR__ . '/../layout/_alerts.php'; ?>

    <div class="card">
        <form method="POST" action="/inscripciones/individual/crear">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
            <input type="hidden" name="actividad_id" value="<?= (int) $actividad['id'] ?>">

            <div class="grid-2">
                <div class="field">
                    <label>Nombre</label>
                    <input type="text" name="nombre" value="<?= htmlspecialchars($datos['nombre'] ?? '') ?>" <?= $esParticipante ? 'readonly' : '' ?> required>
                </div>
                <div class="field">
                    <label>Apellido</label>
                    <input type="text" name="apellido" value="<?= htmlspecialchars($datos['apellido'] ?? '') ?>" <?= $esParticipante ? 'readonly' : '' ?> required>
                </div>
            </div>
            <div class="grid-2">
                <div class="field">
                    <label>Correo electronico</label>
                    <input type="email" name="correo" value="<?= htmlspecialchars($datos['correo'] ?? '') ?>" <?= $esParticipante ? 'readonly' : '' ?> required>
                </div>
                <div class="field">
                    <label>Telefono</label>
                    <input type="text" name="telefono" value="<?= htmlspecialchars($datos['telefono'] ?? '') ?>">
                </div>
            </div>
            <div class="field">
                <label>Edad</label>
                <input type="number" name="edad" min="5" max="100" value="<?= htmlspecialchars($datos['edad'] ?? '') ?>" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">Confirmar e ir a factura</button>
        </form>
    </div>
</div>
