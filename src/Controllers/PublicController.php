<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Actividad;
use App\Models\InscripcionEquipo;
use App\Models\MensajeContacto;
use App\Utils\Sanitizacion;
use App\Utils\Validaciones;

/**
 * Requisito #17: Pagina publica (contactenos, importancia, stack).
 * Requisito #10: Pagina destino del codigo QR de cada actividad.
 */
final class PublicController extends Controller
{
    public function inicio(): void
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        // Incluye actividades proximas y las que ya comenzaron, siempre que
        // sigan publicadas y con inscripciones abiertas.
        $actividades = (new Actividad())->vigentesParaParticipante();
        $this->render('public/inicio', [
            'actividades' => $actividades,
            'errores' => $this->getErrors(),
            'exito' => $this->getSuccess(),
            'csrf' => $_SESSION['csrf_token'],
        ], 'layout/guest');
    }

    public function evento(string $token): void
    {
        $modelo = new Actividad();
        $actividad = $modelo->buscarPorToken($token);

        if (!$actividad) {
            http_response_code(404);
            $this->render('errors/404', [], 'layout/guest');
            return;
        }

        $equipos = [];
        if ($actividad['modalidad'] !== 'INDIVIDUAL') {
            $equipos = array_filter(
                (new InscripcionEquipo())->porActividad((int) $actividad['id']),
                static fn($i) => $i['estado'] === 'APROBADA'
            );
        }

        $this->render('public/evento', [
            'actividad' => $actividad,
            'equipos' => $equipos,
            'admiteInscripcion' => $modelo->admiteInscripcion($actividad),
        ], 'layout/guest');
    }

    public function contacto(): void
    {
        $this->verifyCsrf();

        $datos = [
            'nombre' => Sanitizacion::texto($_POST['nombre'] ?? ''),
            'correo' => Sanitizacion::email($_POST['correo'] ?? ''),
            'telefono' => Sanitizacion::texto($_POST['telefono'] ?? ''),
            'asunto' => Sanitizacion::texto($_POST['asunto'] ?? ''),
            'mensaje' => Sanitizacion::texto($_POST['mensaje'] ?? ''),
        ];

        $errores = Validaciones::validar([
            fn() => Validaciones::requerido($datos['nombre'], 'nombre'),
            fn() => Validaciones::email($datos['correo']),
            fn() => Validaciones::requerido($datos['asunto'], 'asunto'),
            fn() => Validaciones::requerido($datos['mensaje'], 'mensaje'),
        ]);

        if (!empty($errores)) {
            $this->flashErrors($errores);
            $this->redirect('/#contacto');
        }

        (new MensajeContacto())->crear($datos);
        $this->flashSuccess('¡Gracias! Tu mensaje fue enviado, te contactaremos pronto.');
        $this->redirect('/#contacto');
    }
}
