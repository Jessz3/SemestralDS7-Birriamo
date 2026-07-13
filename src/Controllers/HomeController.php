<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Estadistica;
use App\Models\MensajeContacto;

final class HomeController extends Controller
{
    public function dashboard(): void
    {
        $this->requireAuth();
        $this->render('public/dashboard', [
            'resumen' => (new Estadistica())->resumenGeneral(),
            'mensajesNuevos' => count(array_filter((new MensajeContacto())->todos(), static fn($m) => $m['estado'] === 'NUEVO')),
        ]);
    }
}
