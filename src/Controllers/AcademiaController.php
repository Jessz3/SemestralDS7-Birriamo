<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Academia;
use App\Models\Deporte;
use App\Utils\Sanitizacion;
use App\Utils\Validaciones;

/** Requisito #5: Modulo de Academias Deportivas. */
final class AcademiaController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();
        $this->render('academias/index', [
            'academias' => (new Academia())->todos(),
            'exito' => $this->getSuccess(),
        ]);
    }

    public function crearForm(): void
    {
        $this->requireAuth();
        $this->render('academias/crear', [
            'deportes' => (new Deporte())->todos(true),
            'errores' => $this->getErrors(),
            'datos' => $this->oldInput(),
            'csrf' => $_SESSION['csrf_token'],
        ]);
    }

    private function recolectar(): array
    {
        return [
            'nombre' => Sanitizacion::texto($_POST['nombre'] ?? ''),
            'ruc' => Sanitizacion::texto($_POST['ruc'] ?? ''),
            'descripcion' => Sanitizacion::texto($_POST['descripcion'] ?? ''),
            'correo' => Sanitizacion::email($_POST['correo'] ?? ''),
            'telefono' => Sanitizacion::texto($_POST['telefono'] ?? ''),
            'direccion' => Sanitizacion::texto($_POST['direccion'] ?? ''),
            'deportes' => array_map('intval', $_POST['deportes'] ?? []),
        ];
    }

    public function crear(): void
    {
        $this->requireAuth();
        $this->verifyCsrf();

        $datos = $this->recolectar();
        $errores = Validaciones::validar([
            fn() => Validaciones::requerido($datos['nombre'], 'nombre'),
        ]);

        if (!empty($errores)) {
            $this->flashErrors($errores, $datos);
            $this->redirect('/academias/crear');
        }

        (new Academia())->crear($datos);
        $this->flashSuccess('Academia registrada correctamente.');
        $this->redirect('/academias');
    }

    public function editarForm(): void
    {
        $this->requireAuth();
        $modelo = new Academia();
        $academia = $modelo->buscarPorId((int) ($_GET['id'] ?? 0));
        if (!$academia) {
            $this->redirect('/academias');
        }
        $deportesSeleccionados = array_column($modelo->deportesDeAcademia((int) $academia['id']), 'id');

        $this->render('academias/editar', [
            'academia' => $academia,
            'deportes' => (new Deporte())->todos(true),
            'deportesSeleccionados' => $deportesSeleccionados,
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

        (new Academia())->actualizar($id, $datos);
        $this->flashSuccess('Academia actualizada correctamente.');
        $this->redirect('/academias');
    }

    public function deshabilitar(): void
    {
        $this->requireAuth();
        $this->verifyCsrf($_GET['csrf_token'] ?? null);
        (new Academia())->cambiarEstado((int) ($_GET['id'] ?? 0), false);
        $this->redirect('/academias');
    }

    public function habilitar(): void
    {
        $this->requireAuth();
        $this->verifyCsrf($_GET['csrf_token'] ?? null);
        (new Academia())->cambiarEstado((int) ($_GET['id'] ?? 0), true);
        $this->redirect('/academias');
    }
}
