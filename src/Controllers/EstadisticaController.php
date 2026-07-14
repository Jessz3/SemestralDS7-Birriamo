<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Estadistica;
use App\Models\Organizador;

/** Requisito #19: Estadisticas de eventos, incidentes y desempeno de arbitros. */
final class EstadisticaController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();
        $modelo = new Estadistica();

        $organizadorId = null;
        $alcanceEstadisticas = 'del Sistema';

        if (($_SESSION['usuario_rol'] ?? '') === 'ORGANIZADOR') {
            $organizador = (new Organizador())->buscarPorUsuarioId((int) $_SESSION['usuario_id']);
            if (!$organizador) {
                throw new \RuntimeException('La cuenta no tiene un perfil de organizador asociado.');
            }

            $organizadorId = (int) $organizador['id'];
            $alcanceEstadisticas = 'de tu organizacion';
        }

        $this->render('estadisticas/index', [
            'resumen' => $modelo->resumenGeneral($organizadorId),
            'porDeporte' => $modelo->actividadesPorDeporte($organizadorId),
            'rankingArbitros' => $modelo->rankingArbitros($organizadorId),
            'incidentesPorTipo' => $modelo->incidentesPorTipo($organizadorId),
            'recaudacionPorMes' => $modelo->recaudacionPorMes($organizadorId),
            'alcanceEstadisticas' => $alcanceEstadisticas,
        ]);
    }
}
