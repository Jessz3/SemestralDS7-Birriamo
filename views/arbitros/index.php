<div class="container">
    <div class="page-head">
        <div><div class="eyebrow">Arbitraje</div><h1>Arbitros</h1></div>
        <a class="btn btn-primary" href="/arbitros/crear">+ Nuevo arbitro</a>
    </div>

    <?php require __DIR__ . '/../layout/_alerts.php'; ?>

    <div class="card">
        <table class="data-table">
            <thead><tr><th>Nombre</th><th>Licencia</th><th>Promedio general ⭐</th><th>Evaluaciones</th></tr></thead>
            <tbody>
                <?php foreach ($arbitros as $a): ?>
                    <tr>
                        <td><?= htmlspecialchars($a['nombre_completo']) ?></td>
                        <td><?= htmlspecialchars($a['licencia'] ?? '-') ?></td>
                        <td><?= $a['promedio_general'] ?? 'Sin evaluar' ?></td>
                        <td><?= (int) ($a['total_evaluaciones'] ?? 0) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
