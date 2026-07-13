<div class="container">
    <div class="page-head">
        <div>
            <div class="eyebrow">Panel general</div>
            <h1>Bienvenido, <?= htmlspecialchars($_SESSION['usuario_nombre'] ?? '') ?></h1>
        </div>
    </div>

    <div class="grid-4">
        <div class="stat-card">
            <div class="value"><?= (int) $resumen['totalActividades'] ?></div>
            <div class="label">Actividades totales</div>
        </div>
        <div class="stat-card">
            <div class="value"><?= (int) $resumen['actividadesFinalizadas'] ?></div>
            <div class="label">Actividades finalizadas</div>
        </div>
        <div class="stat-card">
            <div class="value">$<?= number_format((float) $resumen['totalRecaudado'], 2) ?></div>
            <div class="label">Total recaudado</div>
        </div>
        <div class="stat-card">
            <div class="value"><?= (int) $resumen['totalInscripciones'] ?></div>
            <div class="label">Inscripciones registradas</div>
        </div>
    </div>

    <div class="card" style="margin-top:1.5rem;">
        <h2>Accesos rapidos</h2>
        <div class="grid-4">
            <a class="btn btn-primary" href="/actividades/crear">+ Nueva actividad</a>
            <a class="btn btn-outline" href="/equipos/crear">+ Nuevo equipo</a>
            <a class="btn btn-outline" href="/facturas">Ver facturas</a>
            <a class="btn btn-outline" href="/estadisticas">Ver estadisticas</a>
        </div>
    </div>

    <?php if ($mensajesNuevos > 0): ?>
        <div class="card">
            <div class="card-header-row">
                <h2>Mensajes de contacto</h2>
                <span class="badge badge-warning"><?= $mensajesNuevos ?> nuevo(s)</span>
            </div>
            <a class="btn btn-outline btn-sm" href="/configuracion/mensajes">Ver bandeja de mensajes</a>
        </div>
    <?php endif; ?>
</div>
