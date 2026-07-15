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

/**
 * Modulo de Entrenadores.
 */
final class EntrenadorController extends Controller
{
    /**
     * Administrador, Operador y Organizador pueden consultar entrenadores.
     */
    public function index(): void
    {
        $this->requireRole(
            'ADMINISTRADOR',
            'OPERADOR',
            'ORGANIZADOR'
        );

        $this->render('entrenadores/index', [
            'entrenadores' => (new Entrenador())->todos(),
            'exito' => $this->getSuccess(),
        ]);
    }

    /**
     * Solo Administrador y Operador pueden registrar entrenadores.
     */
    public function crearForm(): void
    {
        $this->requireRole(
            'ADMINISTRADOR',
            'OPERADOR'
        );

        $this->render('entrenadores/crear', [
            'academias' => (new Academia())->todos(),
            'organizadores' => (new Organizador())->todos(),
            'deportes' => (new Deporte())->todos(true),
            'errores' => $this->getErrors(),
            'datos' => $this->oldInput(),
            'csrf' => $_SESSION['csrf_token'],
        ]);
    }

    public function crear(): void
    {
        $this->requireRole(
            'ADMINISTRADOR',
            'OPERADOR'
        );

        $this->verifyCsrf();

        $rawAnios = $_POST['anios_experiencia'] ?? '';
        $rawAcademia = $_POST['academia_id'] ?? '';
        $rawOrganizador = $_POST['organizador_id'] ?? '';

        $datos = [
            'nombre_completo' => Sanitizacion::texto(
                $_POST['nombre_completo'] ?? ''
            ),
            'cedula' => Sanitizacion::texto(
                $_POST['cedula'] ?? ''
            ),
            'correo' => Sanitizacion::email(
                $_POST['correo'] ?? ''
            ),
            'telefono' => Sanitizacion::texto(
                $_POST['telefono'] ?? ''
            ),
            'certificaciones' => Sanitizacion::texto(
                $_POST['certificaciones'] ?? ''
            ),
            'anios_experiencia' => Sanitizacion::entero(
                $rawAnios
            ) ?: null,
            'academia_id' => Sanitizacion::entero(
                $rawAcademia
            ) ?: null,
            'organizador_id' => Sanitizacion::entero(
                $rawOrganizador
            ) ?: null,
            'deportes' => array_map(
                'intval',
                $_POST['deportes'] ?? []
            ),
        ];

        $errores = Validaciones::validar([
            fn() => Validaciones::requerido(
                $datos['nombre_completo'],
                'nombre completo'
            ),

            fn() => Validaciones::requerido(
                $datos['correo'],
                'correo'
            ),

            fn() => Validaciones::email(
                $datos['correo']
            ),

            fn() => Validaciones::requerido(
                $datos['telefono'],
                'telefono'
            ),

            fn() => Validaciones::celularPanama($datos['telefono']),

            fn() => $rawAnios !== ''
                ? Validaciones::enteroPositivo(
                    $rawAnios,
                    'años de experiencia'
                )
                : null,

            fn() => $rawAcademia !== ''
                ? Validaciones::entero(
                    $rawAcademia,
                    'academia'
                )
                : null,

            fn() => $rawOrganizador !== ''
                ? Validaciones::entero(
                    $rawOrganizador,
                    'organizador'
                )
                : null,
        ]);

        if (!empty($errores)) {
            $this->flashErrors($errores, $datos);
            $this->redirect('/entrenadores/crear');
        }

        (new Entrenador())->crear($datos);

        $this->flashSuccess(
            'Entrenador registrado correctamente.'
        );

        $this->redirect('/entrenadores');
    }
}
