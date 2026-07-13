<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Actividad;
use App\Models\Equipo;
use App\Models\Estadistica;
use App\Models\InscripcionEquipo;
use App\Models\InscripcionIndividual;
use App\Models\MensajeContacto;
use App\Models\Participante;

final class HomeController extends Controller
{
    public function dashboard(): void
    {
        $this->requireAuth();

        if (($_SESSION['usuario_rol'] ?? '') === 'PARTICIPANTE') {
            $participante = (new Participante())->buscarPorUsuarioId((int) $_SESSION['usuario_id']);
            if (!$participante) {
                throw new \RuntimeException('La cuenta no tiene un perfil de participante asociado.');
            }

            $participanteId = (int) $participante['id'];
            $this->render('public/dashboard_participante', [
                'actividades' => (new Actividad())->vigentesParaParticipante(),
                'equipos' => (new Equipo())->porParticipante($participanteId),
                'inscripcionesIndividuales' => (new InscripcionIndividual())->porParticipante($participanteId),
                'inscripcionesEquipo' => (new InscripcionEquipo())->porParticipante($participanteId),
                'exito' => $this->getSuccess(),
            ]);
            return;
        }

        $this->render('public/dashboard', [
            'resumen' => (new Estadistica())->resumenGeneral(),
            'mensajesNuevos' => count(array_filter((new MensajeContacto())->todos(), static fn($m) => $m['estado'] === 'NUEVO')),
        ]);
    }
}
