<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Actividad;
use App\Models\Arbitro;
use App\Models\Deporte;
use App\Models\Organizador;
use App\Utils\Sanitizacion;
use App\Utils\Validaciones;

/**
 * Requisito #19: Arbitros y su desempeno evaluado por el organizador.
 */
final class ArbitroController extends Controller
{
    /**
     * Administrador, Operador y Organizador pueden consultar árbitros.
     */
    public function index(): void
    {
        $this->requireRole(
            'ADMINISTRADOR',
            'OPERADOR',
            'ORGANIZADOR'
        );

        $this->render('arbitros/index', [
            'arbitros' => (new Arbitro())->todosConDesempeno(),
            'exito' => $this->getSuccess(),
        ]);
    }

    /**
     * Solo Administrador y Operador pueden registrar árbitros.
     */
    public function crearForm(): void
    {
        $this->requireRole(
            'ADMINISTRADOR',
            'OPERADOR'
        );

        $this->render('arbitros/crear', [
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

        $datos = [
            'nombre_completo' => Sanitizacion::texto($_POST['nombre_completo'] ?? ''),
            'cedula' => Sanitizacion::texto($_POST['cedula'] ?? ''),
            'correo' => Sanitizacion::email($_POST['correo'] ?? ''),
            'telefono' => Sanitizacion::texto($_POST['telefono'] ?? ''),
            'licencia' => Sanitizacion::texto($_POST['licencia'] ?? ''),
            'experiencia' => Sanitizacion::texto($_POST['experiencia'] ?? ''),
            'deportes' => array_map('intval', $_POST['deportes'] ?? []),
        ];

        $errores = Validaciones::validar([
            fn() => Validaciones::requerido(
                $datos['nombre_completo'],
                'nombre completo'
            ),
            fn() => Validaciones::requerido(
                $datos['telefono'],
                'telefono'
            ),
            fn() => Validaciones::celularPanama($datos['telefono']),
        ]);

        if (!empty($errores)) {
            $this->flashErrors($errores, $datos);
            $this->redirect('/arbitros/crear');
        }

        (new Arbitro())->crear($datos);

        $this->flashSuccess('Arbitro registrado correctamente.');
        $this->redirect('/arbitros');
    }

    /**
     * Solo el Organizador puede evaluar árbitros.
     */
    public function evaluarForm(): void
    {
        $this->requireRole('ORGANIZADOR');

        $actividadId = (int) ($_GET['actividad_id'] ?? 0);
        $arbitroId = (int) ($_GET['arbitro_id'] ?? 0);

        $actividad = (new Actividad())->buscarPorId($actividadId);
        $arbitro = (new Arbitro())->buscarPorId($arbitroId);
        $organizador = $this->organizadorActual();

        if (!$actividad || !$arbitro) {
            $this->redirect('/actividades');
        }

        if ((int) $actividad['organizador_id'] !== (int) $organizador['id']) {
            http_response_code(403);
            $this->render('errors/403');
            exit;
        }

        $this->render('arbitros/evaluar', [
            'actividad' => $actividad,
            'arbitro' => $arbitro,
            'organizador' => $organizador,
            'errores' => $this->getErrors(),
            'csrf' => $_SESSION['csrf_token'],
        ]);
    }

    public function evaluar(): void
    {
        $this->requireRole('ORGANIZADOR');

        $this->verifyCsrf();

        $actividadId = (int) ($_POST['actividad_id'] ?? 0);
        $arbitroId = (int) ($_POST['arbitro_id'] ?? 0);

        $organizador = $this->organizadorActual();
        $organizadorId = (int) $organizador['id'];
        $actividad = (new Actividad())->buscarPorId($actividadId);
        $arbitro = (new Arbitro())->buscarPorId($arbitroId);

        if (!$actividad || !$arbitro) {
            $this->redirect('/actividades');
        }

        if ((int) $actividad['organizador_id'] !== $organizadorId) {
            http_response_code(403);
            $this->render('errors/403');
            exit;
        }

        $puntuacion = Sanitizacion::entero(
            $_POST['puntuacion'] ?? 0
        );

        $puntualidad = Sanitizacion::entero(
            $_POST['puntualidad'] ?? 0
        ) ?: null;

        $reglas = Sanitizacion::entero(
            $_POST['conocimiento_reglas'] ?? 0
        ) ?: null;

        $imparcialidad = Sanitizacion::entero(
            $_POST['imparcialidad'] ?? 0
        ) ?: null;

        $manejo = Sanitizacion::entero(
            $_POST['manejo_actividad'] ?? 0
        ) ?: null;

        $comentario = Sanitizacion::texto(
            $_POST['comentario'] ?? ''
        );

        $errores = Validaciones::validar([
            fn() => Validaciones::rangoNumerico(
                (float) $puntuacion,
                1,
                5,
                'puntuacion general'
            ),
        ]);

        if (!empty($errores)) {
            $this->flashErrors($errores);

            $this->redirect(
                '/arbitros/evaluar?actividad_id='
                . $actividadId
                . '&arbitro_id='
                . $arbitroId
            );
        }

        (new Arbitro())->registrarEvaluacion(
            $actividadId,
            $arbitroId,
            $organizadorId,
            $puntuacion,
            $puntualidad,
            $reglas,
            $imparcialidad,
            $manejo,
            $comentario ?: null
        );

        $this->flashSuccess(
            'Evaluacion registrada correctamente.'
        );

        $this->redirect(
            '/actividades/ver?id=' . $actividadId
        );
    }

    private function organizadorActual(): array
    {
        $organizador = (new Organizador())->buscarPorUsuarioId(
            (int) ($_SESSION['usuario_id'] ?? 0)
        );

        if (!$organizador) {
            throw new \RuntimeException(
                'La cuenta no tiene un perfil de organizador asociado.'
            );
        }

        return $organizador;
    }
}
