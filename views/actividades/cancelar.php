<div class="container" style="max-width:560px;">
    <div class="page-head"><div><div class="eyebrow">Actividad</div><h1>Cancelar Actividad</h1></div></div>

    <div class="card">
        <div class="alert alert-danger">
            Al cancelar, todas las inscripciones asociadas pasaran a estado CANCELADA y se generara
            automaticamente una solicitud de devolucion por cada factura ya emitida.
        </div>
        <form method="POST" action="/actividades/cancelar">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
            <input type="hidden" name="id" value="<?= (int) $actividadId ?>">

            <div class="field">
                <label>Motivo de la cancelacion</label>
                <textarea name="motivo" rows="3" minlength="5" maxlength="1000" required></textarea>
            </div>

            <button type="submit" class="btn btn-danger">Confirmar cancelacion</button>
            <a class="btn btn-outline" href="/actividades/ver?id=<?= (int) $actividadId ?>">Volver</a>
        </form>
    </div>
</div>
