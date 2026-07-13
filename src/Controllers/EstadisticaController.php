<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Estadistica;

/** Requisito #19: Estadisticas de eventos, incidentes y desempeno de arbitros. */
final class EstadisticaController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();
        $modelo = new Estadistica();

        $this->render('estadisticas/index', [
            'resumen' => $modelo->resumenGeneral(),
            'porDeporte' => $modelo->actividadesPorDeporte(),
            'rankingArbitros' => $modelo->rankingArbitros(),
            'incidentesPorTipo' => $modelo->incidentesPorTipo(),
            'recaudacionPorMes' => $modelo->recaudacionPorMes(),
        ]);
    }
}
