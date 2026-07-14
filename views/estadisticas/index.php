<?php
$resumen = $resumen ?? [
    'totalActividades' => 0,
    'totalIncidentes' => 0,
    'totalRecaudado' => 0,
    'totalEquipos' => 0,
    'totalInscripciones' => 0,
];
$porDeporte = $porDeporte ?? [];
$rankingArbitros = $rankingArbitros ?? [];
$incidentesPorTipo = $incidentesPorTipo ?? [];
$recaudacionPorMes = $recaudacionPorMes ?? [];

$maxDeporte = max(array_column($porDeporte, 'total') ?: [1]) ?: 1;
$maxRecaudo = max(array_column($recaudacionPorMes, 'total') ?: [1]) ?: 1;
?>
<div class="container">
    <div class="page-head"><div><div class="eyebrow">Analitica</div><h1>Estadisticas <?= htmlspecialchars($alcanceEstadisticas ?? 'del Sistema') ?></h1></div></div>

    <div class="grid-4">
        <div class="stat-card"><div class="value"><?= (int) $resumen['totalActividades'] ?></div><div class="label">Actividades</div></div>
        <div class="stat-card"><div class="value"><?= (int) $resumen['totalIncidentes'] ?></div><div class="label">Incidentes</div></div>
        <div class="stat-card"><div class="value">$<?= number_format((float) $resumen['totalRecaudado'], 2) ?></div><div class="label">Recaudado</div></div>
        <div class="stat-card"><div class="value"><?= (int) $resumen['totalEquipos'] ?></div><div class="label">Equipos</div></div>
    </div>

    <div class="grid-2" style="margin-top:1.5rem;">
        <div class="card">
            <h2>Actividades por Deporte</h2>
            <?php foreach ($porDeporte as $pd): ?>
                <div style="margin-bottom:.6rem;">
                    <div style="display:flex;justify-content:space-between;font-size:.82rem;">
                        <span><?= htmlspecialchars($pd['deporte']) ?></span><span><?= (int) $pd['total'] ?></span>
                    </div>
                    <div style="background:#eef1f4;border-radius:6px;height:10px;">
                        <div style="background:var(--color-turf);width:<?= max(4, (int) $pd['total'] / $maxDeporte * 100) ?>%;height:10px;border-radius:6px;"></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="card">
            <h2>Recaudacion por Mes</h2>
            <?php if (empty($recaudacionPorMes)): ?>
                <div class="empty-state">Aun no hay facturas emitidas.</div>
            <?php endif; ?>
            <?php foreach ($recaudacionPorMes as $rm): ?>
                <div style="margin-bottom:.6rem;">
                    <div style="display:flex;justify-content:space-between;font-size:.82rem;">
                        <span><?= htmlspecialchars($rm['mes']) ?></span><span>$<?= number_format((float) $rm['total'], 2) ?></span>
                    </div>
                    <div style="background:#eef1f4;border-radius:6px;height:10px;">
                        <div style="background:var(--color-amber);width:<?= max(4, (float) $rm['total'] / $maxRecaudo * 100) ?>%;height:10px;border-radius:6px;"></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="grid-2">
        <div class="card">
            <h2>Ranking de Arbitros</h2>
            <?php if (empty($rankingArbitros)): ?>
                <div class="empty-state">Aun no hay evaluaciones registradas.</div>
            <?php else: ?>
                <table class="data-table">
                    <thead><tr><th>Arbitro</th><th>Promedio ⭐</th><th>Evaluaciones</th></tr></thead>
                    <tbody>
                        <?php foreach ($rankingArbitros as $r): ?>
                            <tr>
                                <td><?= htmlspecialchars($r['nombre_completo']) ?></td>
                                <td><?= htmlspecialchars($r['promedio_general']) ?></td>
                                <td><?= (int) $r['total_evaluaciones'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <div class="card">
            <h2>Incidentes por Tipo</h2>
            <?php if (empty($incidentesPorTipo)): ?>
                <div class="empty-state">Sin incidentes registrados.</div>
            <?php else: ?>
                <table class="data-table">
                    <thead><tr><th>Tipo</th><th>Total</th></tr></thead>
                    <tbody>
                        <?php foreach ($incidentesPorTipo as $it): ?>
                            <tr><td><?= htmlspecialchars($it['tipo']) ?></td><td><?= (int) $it['total'] ?></td></tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>
