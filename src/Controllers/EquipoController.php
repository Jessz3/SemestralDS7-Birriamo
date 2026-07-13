<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Academia;
use App\Models\Bitacora;
use App\Models\Deporte;
use App\Models\Equipo;
use App\Models\Participante;
use App\Utils\Sanitizacion;
use App\Utils\Validaciones;

/** Requisito #7: Modulo de equipos (dueno = un Participante con cuenta propia). */
final class EquipoController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();
        $equipos = (new Equipo())->todos();
        if (($_SESSION['usuario_rol'] ?? '') === 'PARTICIPANTE') {
            $perfil = (new Participante())->buscarPorUsuarioId((int) $_SESSION['usuario_id']);
            $equipos = $perfil ? (new Equipo())->porParticipante((int) $perfil['id']) : [];
        }
        $this->render('equipos/index', [
            'equipos' => $equipos,
            'exito' => $this->getSuccess(),
        ]);
    }

    public function crearForm(): void
    {
        $this->requireAuth();
        $this->render('equipos/crear', [
            'academias' => (new Academia())->todos(),
            'deportes' => (new Deporte())->todos(true),
            'errores' => $this->getErrors(),
            'datos' => $this->oldInput(),
            'csrf' => $_SESSION['csrf_token'],
            'esParticipante' => ($_SESSION['usuario_rol'] ?? '') === 'PARTICIPANTE',
        ]);
    }

    private function procesarAvatar(): ?string
    {
        if (empty($_FILES['avatar']['name'])) {
            return null;
        }

        $permitidos = ['image/jpeg', 'image/png', 'image/webp'];
        $tipo = mime_content_type($_FILES['avatar']['tmp_name']);

        if (!in_array($tipo, $permitidos, true) || $_FILES['avatar']['size'] > 2 * 1024 * 1024) {
            return null;
        }

        $extension = match ($tipo) {
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => 'jpg',
        };

        $nombreArchivo = Sanitizacion::nombreArchivo(uniqid('equipo_', true)) . '.' . $extension;
        $destino = __DIR__ . '/../../public/uploads/avatars/' . $nombreArchivo;
        move_uploaded_file($_FILES['avatar']['tmp_name'], $destino);

        return '/uploads/avatars/' . $nombreArchivo;
    }

    public function crear(): void
    {
        $this->requireAuth();
        $this->verifyCsrf();

        $datos = [
            'nombre' => Sanitizacion::texto($_POST['nombre'] ?? ''),
            'deporte_id' => Sanitizacion::entero($_POST['deporte_id'] ?? 0),
            'academia_id' => Sanitizacion::entero($_POST['academia_id'] ?? 0) ?: null,
            'descripcion' => Sanitizacion::texto($_POST['descripcion'] ?? ''),
            'rep_nombre' => Sanitizacion::texto($_POST['rep_nombre'] ?? ''),
            'rep_apellido' => Sanitizacion::texto($_POST['rep_apellido'] ?? ''),
            'rep_correo' => Sanitizacion::email($_POST['rep_correo'] ?? ''),
            'rep_telefono' => Sanitizacion::texto($_POST['rep_telefono'] ?? ''),
        ];

        $perfilParticipante = null;
        if (($_SESSION['usuario_rol'] ?? '') === 'PARTICIPANTE') {
            $perfilParticipante = (new Participante())->buscarPorUsuarioId((int) $_SESSION['usuario_id']);
            if (!$perfilParticipante) {
                throw new \RuntimeException('La cuenta no tiene un perfil de participante asociado.');
            }
            $participanteCompleto = (new Participante())->buscarPorId((int) $perfilParticipante['id']);
            $datos['rep_nombre'] = $participanteCompleto['nombre'];
            $datos['rep_apellido'] = $participanteCompleto['apellido'];
            $datos['rep_correo'] = $participanteCompleto['correo'];
            $datos['rep_telefono'] = $participanteCompleto['telefono'] ?? '';
        }

        $errores = Validaciones::validar([
            fn() => Validaciones::requerido($datos['nombre'], 'nombre del equipo'),
            fn() => Validaciones::requerido((string) $datos['deporte_id'], 'deporte'),
            fn() => Validaciones::requerido($datos['rep_nombre'], 'nombre del representante'),
            fn() => Validaciones::requerido($datos['rep_apellido'], 'apellido del representante'),
            fn() => Validaciones::email($datos['rep_correo']),
        ]);

        if (!empty($errores)) {
            $this->flashErrors($errores, $datos);
            $this->redirect('/equipos/crear');
        }

        $participante = $perfilParticipante
            ? ['participante_id' => (int) $perfilParticipante['id']]
            : (new Participante())->encontrarOCrear(
                $datos['rep_nombre'], $datos['rep_apellido'], $datos['rep_correo'], $datos['rep_telefono']
            );

        $datos['avatar'] = $this->procesarAvatar();
        $id = (new Equipo())->crear($participante['participante_id'], $datos);

        (new Bitacora())->registrar(
            (int) $_SESSION['usuario_id'],
            'EQUIPOS',
            'CREAR',
            'equipos',
            (string) $id,
            "Equipo {$datos['nombre']} registrado para el representante {$datos['rep_correo']}."
        );

        $this->flashSuccess('Equipo registrado correctamente.');
        $this->redirect('/equipos/ver?id=' . $id);
    }

    public function ver(): void
    {
        $this->requireAuth();
        $modelo = new Equipo();
        $id = (int) ($_GET['id'] ?? 0);
        $equipo = $modelo->buscarPorId($id);

        if (!$equipo) {
            $this->redirect('/equipos');
        }
        $this->verificarPropiedadParticipante($id);

        $this->render('equipos/ver', [
            'equipo' => $equipo,
            'jugadores' => $modelo->jugadoresDeEquipo($id),
            'errores' => $this->getErrors(),
            'exito' => $this->getSuccess(),
            'csrf' => $_SESSION['csrf_token'],
        ]);
    }

    public function agregarJugador(): void
    {
        $this->requireAuth();
        $this->verifyCsrf();

        $equipoId = (int) ($_POST['equipo_id'] ?? 0);
        $this->verificarPropiedadParticipante($equipoId);
        $datos = [
            'nombre_completo' => Sanitizacion::texto($_POST['nombre_completo'] ?? ''),
            'edad' => Sanitizacion::entero($_POST['edad'] ?? 0),
            'peso_kg' => Sanitizacion::decimal($_POST['peso_kg'] ?? 0) ?: null,
            'posicion' => Sanitizacion::texto($_POST['posicion'] ?? ''),
            'numero_camiseta' => Sanitizacion::entero($_POST['numero_camiseta'] ?? 0) ?: null,
            'capitan' => isset($_POST['capitan']) ? 1 : 0,
        ];

        $errores = Validaciones::validar([
            fn() => Validaciones::requerido($datos['nombre_completo'], 'nombre del jugador'),
            fn() => Validaciones::rangoNumerico((float) $datos['edad'], 5, 80, 'edad'),
        ]);

        if (!empty($errores)) {
            $this->flashErrors($errores);
            $this->redirect('/equipos/ver?id=' . $equipoId);
        }

        (new Equipo())->agregarJugador($equipoId, $datos);
        $this->flashSuccess('Jugador agregado al equipo.');
        $this->redirect('/equipos/ver?id=' . $equipoId);
    }

    public function eliminarJugador(): void
    {
        $this->requireAuth();
        $equipoId = (int) ($_GET['equipo_id'] ?? 0);
        $this->verificarPropiedadParticipante($equipoId);
        (new Equipo())->eliminarJugador((int) ($_GET['jugador_id'] ?? 0), $equipoId);
        $this->redirect('/equipos/ver?id=' . $equipoId);
    }

    private function verificarPropiedadParticipante(int $equipoId): void
    {
        if (($_SESSION['usuario_rol'] ?? '') === 'PARTICIPANTE'
            && !(new Equipo())->perteneceAUsuario($equipoId, (int) $_SESSION['usuario_id'])) {
            http_response_code(403);
            $this->render('errors/403');
            exit;
        }
    }
}
