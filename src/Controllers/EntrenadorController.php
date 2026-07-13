<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Academia;
use App\Models\Deporte;
use App\Models\Entrenador;
use App\Models\Organizador;
use App\Utils\Sanitizacion;
use App\Utils\Validaciones;

/** Modulo de Entrenadores, referenciados opcionalmente desde Actividades. */
final class EntrenadorController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();
        $this->render('entrenadores/index', [
            'entrenadores' => (new Entrenador())->todos(),
            'exito' => $this->getSuccess(),
        ]);
    }

    public function crearForm(): void
    {
        $this->requireAuth();
        $this->render('entrenadores/crear', [
            'academias' => (new Academia())->todos(),
            'organizadores' => (new Organizador())->todos(),
            'deportes' => (new Deporte())->todos(true),
            'errores' => $this->getErrors(),
            'csrf' => $_SESSION['csrf_token'],
        ]);
    }

    public function crear(): void
    {
        $this->requireAuth();
        $this->verifyCsrf();

        $datos = [
            'nombre_completo' => Sanitizacion::texto($_POST['nombre_completo'] ?? ''),
            'cedula' => Sanitizacion::texto($_POST['cedula'] ?? ''),
            'correo' => Sanitizacion::email($_POST['correo'] ?? ''),
            'telefono' => Sanitizacion::texto($_POST['telefono'] ?? ''),
            'certificaciones' => Sanitizacion::texto($_POST['certificaciones'] ?? ''),
            'anios_experiencia' => Sanitizacion::entero($_POST['anios_experiencia'] ?? 0) ?: null,
            'academia_id' => Sanitizacion::entero($_POST['academia_id'] ?? 0) ?: null,
            'organizador_id' => Sanitizacion::entero($_POST['organizador_id'] ?? 0) ?: null,
            'deportes' => array_map('intval', $_POST['deportes'] ?? []),
        ];

        $errores = Validaciones::validar([
            fn() => Validaciones::requerido($datos['nombre_completo'], 'nombre completo'),
        ]);

        if (!empty($errores)) {
            $this->flashErrors($errores);
            $this->redirect('/entrenadores/crear');
        }

        (new Entrenador())->crear($datos);
        $this->flashSuccess('Entrenador registrado correctamente.');
        $this->redirect('/entrenadores');
    }
}
