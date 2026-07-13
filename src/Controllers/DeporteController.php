<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Deporte;
use App\Utils\Sanitizacion;
use App\Utils\Validaciones;

/** Requisito #3: Modulo de Deportes (Catalogo). */
final class DeporteController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();
        $modelo = new Deporte();
        $this->render('deportes/index', [
            'deportes' => $modelo->todos(),
            'exito' => $this->getSuccess(),
        ]);
    }

    public function crearForm(): void
    {
        $this->requireAuth();
        $this->render('deportes/crear', [
            'errores' => $this->getErrors(),
            'datos' => $this->oldInput(),
            'csrf' => $_SESSION['csrf_token'],
        ]);
    }

    private function recolectar(): array
    {
        return [
            'nombre' => Sanitizacion::texto($_POST['nombre'] ?? ''),
            'descripcion' => Sanitizacion::texto($_POST['descripcion'] ?? ''),
            'es_equipo' => isset($_POST['es_equipo']) ? 1 : 0,
            'minimo_jugadores' => Sanitizacion::entero($_POST['minimo_jugadores'] ?? 0) ?: null,
            'maximo_jugadores' => Sanitizacion::entero($_POST['maximo_jugadores'] ?? 0) ?: null,
        ];
    }

    public function crear(): void
    {
        $this->requireAuth();
        $this->verifyCsrf();

        $datos = $this->recolectar();
        $modelo = new Deporte();
        $errores = Validaciones::validar([
            fn() => Validaciones::requerido($datos['nombre'], 'nombre'),
            fn() => Validaciones::longitud($datos['nombre'], 3, 100, 'nombre'),
            fn() => $modelo->nombreExiste($datos['nombre']) ? 'Ya existe un deporte con ese nombre.' : null,
        ]);

        if (!empty($errores)) {
            $this->flashErrors($errores, $datos);
            $this->redirect('/deportes/crear');
        }

        $modelo->crear($datos);
        $this->flashSuccess('Deporte registrado correctamente.');
        $this->redirect('/deportes');
    }

    public function editarForm(): void
    {
        $this->requireAuth();
        $modelo = new Deporte();
        $deporte = $modelo->buscarPorId((int) ($_GET['id'] ?? 0));
        if (!$deporte) {
            $this->redirect('/deportes');
        }
        $this->render('deportes/editar', [
            'deporte' => $deporte,
            'errores' => $this->getErrors(),
            'csrf' => $_SESSION['csrf_token'],
        ]);
    }

    public function actualizar(): void
    {
        $this->requireAuth();
        $this->verifyCsrf();

        $id = (int) ($_POST['id'] ?? 0);
        $datos = $this->recolectar();

        $modelo = new Deporte();
        $errores = Validaciones::validar([
            fn() => Validaciones::requerido($datos['nombre'], 'nombre'),
            fn() => $modelo->nombreExiste($datos['nombre'], $id) ? 'Ya existe otro deporte con ese nombre.' : null,
        ]);

        if (!empty($errores)) {
            $this->flashErrors($errores);
            $this->redirect('/deportes/editar?id=' . $id);
        }

        $modelo->actualizar($id, $datos);
        $this->flashSuccess('Deporte actualizado correctamente.');
        $this->redirect('/deportes');
    }

    public function deshabilitar(): void
    {
        $this->requireAuth();
        (new Deporte())->cambiarEstado((int) ($_GET['id'] ?? 0), false);
        $this->redirect('/deportes');
    }

    public function habilitar(): void
    {
        $this->requireAuth();
        (new Deporte())->cambiarEstado((int) ($_GET['id'] ?? 0), true);
        $this->redirect('/deportes');
    }
}
