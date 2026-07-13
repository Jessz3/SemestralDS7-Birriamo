<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Instalacion;
use App\Utils\Sanitizacion;
use App\Utils\Validaciones;

/** Requisito #4: Modulo de Instalaciones Deportivas. */
final class InstalacionController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();
        $this->render('instalaciones/index', [
            'instalaciones' => (new Instalacion())->todos(),
            'exito' => $this->getSuccess(),
        ]);
    }

    public function crearForm(): void
    {
        $this->requireAuth();
        $this->render('instalaciones/crear', [
            'tipos' => Instalacion::TIPOS,
            'errores' => $this->getErrors(),
            'datos' => $this->oldInput(),
            'csrf' => $_SESSION['csrf_token'],
        ]);
    }

    private function recolectar(): array
    {
        return [
            'nombre' => Sanitizacion::texto($_POST['nombre'] ?? ''),
            'tipo' => $_POST['tipo'] ?? 'OTRO',
            'descripcion' => Sanitizacion::texto($_POST['descripcion'] ?? ''),
            'direccion' => Sanitizacion::texto($_POST['direccion'] ?? ''),
            'provincia' => Sanitizacion::texto($_POST['provincia'] ?? ''),
            'distrito' => Sanitizacion::texto($_POST['distrito'] ?? ''),
            'corregimiento' => Sanitizacion::texto($_POST['corregimiento'] ?? ''),
            'espacio_disponible' => Sanitizacion::texto($_POST['espacio_disponible'] ?? ''),
            'capacidad_invitados' => Sanitizacion::entero($_POST['capacidad_invitados'] ?? 0),
            'costo_base' => Sanitizacion::decimal($_POST['costo_base'] ?? 0),
        ];
    }

    public function crear(): void
    {
        $this->requireAuth();
        $this->verifyCsrf();

        $datos = $this->recolectar();

        $errores = Validaciones::validar([
            fn() => Validaciones::requerido($datos['nombre'], 'nombre'),
            fn() => Validaciones::enLista($datos['tipo'], Instalacion::TIPOS, 'tipo'),
            fn() => Validaciones::requerido($datos['direccion'], 'direccion'),
            fn() => Validaciones::rangoNumerico($datos['costo_base'], 0, 1000000, 'costo base'),
        ]);

        if (!empty($errores)) {
            $this->flashErrors($errores, $datos);
            $this->redirect('/instalaciones/crear');
        }

        (new Instalacion())->crear($datos);
        $this->flashSuccess('Instalacion registrada correctamente.');
        $this->redirect('/instalaciones');
    }

    public function editarForm(): void
    {
        $this->requireAuth();
        $instalacion = (new Instalacion())->buscarPorId((int) ($_GET['id'] ?? 0));
        if (!$instalacion) {
            $this->redirect('/instalaciones');
        }
        $this->render('instalaciones/editar', [
            'instalacion' => $instalacion,
            'tipos' => Instalacion::TIPOS,
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

        $errores = Validaciones::validar([
            fn() => Validaciones::requerido($datos['nombre'], 'nombre'),
            fn() => Validaciones::requerido($datos['direccion'], 'direccion'),
        ]);

        if (!empty($errores)) {
            $this->flashErrors($errores);
            $this->redirect('/instalaciones/editar?id=' . $id);
        }

        (new Instalacion())->actualizar($id, $datos);
        $this->flashSuccess('Instalacion actualizada correctamente.');
        $this->redirect('/instalaciones');
    }

    public function deshabilitar(): void
    {
        $this->requireAuth();
        (new Instalacion())->cambiarEstado((int) ($_GET['id'] ?? 0), false);
        $this->redirect('/instalaciones');
    }

    public function habilitar(): void
    {
        $this->requireAuth();
        (new Instalacion())->cambiarEstado((int) ($_GET['id'] ?? 0), true);
        $this->redirect('/instalaciones');
    }
}
