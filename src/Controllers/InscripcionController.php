<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Actividad;
use App\Models\Bitacora;
use App\Models\Equipo;
use App\Models\Factura;
use App\Models\InscripcionEquipo;
use App\Models\InscripcionIndividual;
use App\Models\Pago;
use App\Models\Participante;
use App\Models\Usuario;
use App\Utils\Sanitizacion;
use App\Utils\Validaciones;

/**
 * Requisito #7: Inscripciones por equipo.
 * Requisito #8: Inscripciones individuales.
 * En este esquema, aprobar una inscripcion registra un Pago (APROBADO)
 * y emite la Factura correspondiente a partir de ese pago.
 */
final class InscripcionController extends Controller
{
    public function inscribirEquipoForm(): void
    {
        $this->requireAuth();
        $actividadId = (int) ($_GET['actividad_id'] ?? 0);
        $actividad = (new Actividad())->buscarPorId($actividadId);

        if (!$actividad || $actividad['modalidad'] === 'INDIVIDUAL') {
            $this->redirect('/actividades');
        }

        if (!(new Actividad())->admiteInscripcion($actividad)) {
            $this->flashErrors(['Esta actividad ya no admite inscripciones.']);
            $this->redirect('/actividades');
        }

        $equipos = (new Equipo())->todos();
        if (($_SESSION['usuario_rol'] ?? '') === 'PARTICIPANTE') {
            $perfil = (new Participante())->buscarPorUsuarioId((int) $_SESSION['usuario_id']);
            $equipos = $perfil ? (new Equipo())->porParticipante((int) $perfil['id']) : [];
        }

        $this->render('inscripciones/inscribir_equipo', [
            'actividad' => $actividad,
            'equipos' => array_values(array_filter(
                $equipos,
                static fn(array $e): bool => (int) $e['deporte_id'] === (int) $actividad['deporte_id']
            )),
            'errores' => $this->getErrors(),
            'csrf' => $_SESSION['csrf_token'],
        ]);
    }

    public function inscribirEquipo(): void
    {
        $this->requireAuth();
        $this->verifyCsrf();

        $actividadId = (int) ($_POST['actividad_id'] ?? 0);
        $equipoId = (int) ($_POST['equipo_id'] ?? 0);

        $actividadModelo = new Actividad();
        $inscripcionModelo = new InscripcionEquipo();
        $actividad = $actividadModelo->buscarPorId($actividadId);
        $equipo = (new Equipo())->buscarPorId($equipoId);

        $errores = Validaciones::validar([
            fn() => !$actividad ? 'Actividad no encontrada.' : null,
            fn() => $actividad && !$actividadModelo->admiteInscripcion($actividad) ? 'Esta actividad no admite inscripciones en su estado actual.' : null,
            fn() => !$equipo ? 'Equipo no encontrado.' : null,
            fn() => $actividad && $equipo && (int) $actividad['deporte_id'] !== (int) $equipo['deporte_id']
                ? 'El deporte del equipo no corresponde con el de la actividad.' : null,
            fn() => ($_SESSION['usuario_rol'] ?? '') === 'PARTICIPANTE'
                && !(new Equipo())->perteneceAUsuario($equipoId, (int) $_SESSION['usuario_id'])
                ? 'Solo puede inscribir equipos que le pertenecen.' : null,
            fn() => $inscripcionModelo->yaInscrito($actividadId, $equipoId) ? 'Este equipo ya esta inscrito en la actividad.' : null,
            fn() => $actividad && $actividadModelo->cuposOcupados($actividadId) >= (int) $actividad['cupos_disponibles']
                ? 'El cupo maximo de la actividad ha sido alcanzado.' : null,
        ]);

        if (!empty($errores)) {
            $this->flashErrors($errores);
            $this->redirect('/inscripciones/equipo/crear?actividad_id=' . $actividadId);
        }

        $inscripcionModelo->crear($actividadId, $equipoId);
        $this->flashSuccess('Equipo inscrito. Pendiente de aprobacion del organizador.');
        $this->redirect('/actividades/ver?id=' . $actividadId);
    }

    public function aprobarEquipo(): void
    {
        $this->requireAuth();
        $this->verifyCsrf($_GET['csrf_token'] ?? null);
        $id = (int) ($_GET['id'] ?? 0);
        $inscripcionModelo = new InscripcionEquipo();
        $inscripcion = $inscripcionModelo->buscarPorId($id);

        if (!$inscripcion) {
            $this->redirect('/actividades');
        }

        $inscripcionModelo->aprobar($id, (int) $_SESSION['usuario_id']);

        $equipo = (new Equipo())->buscarPorId((int) $inscripcion['equipo_id']);

        $pagoId = (new Pago())->registrar([
            'inscripcion_equipo_id' => $id,
            'participante_id' => $inscripcion['participante_id'],
            'metodo_pago' => 'EFECTIVO',
            'monto' => $inscripcion['costo_inscripcion'],
            'validado_por' => (int) $_SESSION['usuario_id'],
        ]);

        $facturaId = (new Factura())->emitir([
            'pago_id' => $pagoId,
            'participante_id' => $inscripcion['participante_id'],
            'actividad_id' => $inscripcion['actividad_id'],
            'equipo_id' => $inscripcion['equipo_id'],
            'nombre_cliente' => $equipo['representante'] . ' (' . $equipo['nombre'] . ')',
            'correo_cliente' => $equipo['correo_contacto'] ?? null,
            'subtotal' => $inscripcion['costo_inscripcion'],
            'concepto' => 'Inscripción de equipo a ' . $inscripcion['actividad_nombre'],
        ], (int) $_SESSION['usuario_id']);

        (new Bitacora())->registrar(
            (int) $_SESSION['usuario_id'], 'INSCRIPCIONES', 'APROBAR', 'inscripciones_equipos', (string) $id,
            'Inscripcion de equipo aprobada y factura emitida.'
        );

        $this->flashSuccess('Inscripcion aprobada y factura generada.');
        $this->redirect('/facturas/ver?id=' . $facturaId);
    }

    public function rechazarEquipo(): void
    {
        $this->requireAuth();
        $this->verifyCsrf($_GET['csrf_token'] ?? null);
        $id = (int) ($_GET['id'] ?? 0);
        $inscripcion = (new InscripcionEquipo())->buscarPorId($id);
        (new InscripcionEquipo())->rechazar($id);
        $this->flashSuccess('Inscripcion rechazada.');
        $this->redirect('/actividades/ver?id=' . ($inscripcion['actividad_id'] ?? 0));
    }

    public function inscribirIndividualForm(): void
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        $actividadId = (int) ($_GET['actividad_id'] ?? 0);
        $actividad = (new Actividad())->buscarPorId($actividadId);

        if (!$actividad || $actividad['modalidad'] === 'EQUIPO' || !(new Actividad())->admiteInscripcion($actividad)) {
            $this->redirect('/');
        }

        $esParticipante = ($_SESSION['usuario_rol'] ?? '') === 'PARTICIPANTE';
        $datos = $this->oldInput();
        if ($esParticipante) {
            $usuario = (new Usuario())->buscarPorId((int) $_SESSION['usuario_id']);
            $datos = array_merge([
                'nombre' => $usuario['nombre'],
                'apellido' => $usuario['apellido'],
                'correo' => $usuario['correo'],
                'telefono' => $usuario['telefono'] ?? '',
            ], $datos);
        }

        $this->render('inscripciones/inscribir_individual', [
            'actividad' => $actividad,
            'errores' => $this->getErrors(),
            'datos' => $datos,
            'csrf' => $_SESSION['csrf_token'],
            'esParticipante' => $esParticipante,
        ], $esParticipante ? 'layout/main' : 'layout/guest');
    }

    /**
     * Endpoint publico: un participante externo se inscribe sin necesidad
     * de iniciar sesión. Se crea (o reutiliza) su cuenta de PARTICIPANTE,
     * se registra el pago como aprobado, y se emite la factura de inmediato.
     */
    public function inscribirIndividual(): void
    {
        $this->verifyCsrf();

        $actividadId = (int) ($_POST['actividad_id'] ?? 0);
        $actividadModelo = new Actividad();
        $actividad = $actividadModelo->buscarPorId($actividadId);
        $esParticipante = ($_SESSION['usuario_rol'] ?? '') === 'PARTICIPANTE';

        $datos = [
            'nombre' => Sanitizacion::texto($_POST['nombre'] ?? ''),
            'apellido' => Sanitizacion::texto($_POST['apellido'] ?? ''),
            'correo' => Sanitizacion::email($_POST['correo'] ?? ''),
            'telefono' => Sanitizacion::texto($_POST['telefono'] ?? ''),
            'edad' => Sanitizacion::entero($_POST['edad'] ?? 0),
        ];

        $perfilActual = null;
        if ($esParticipante) {
            $perfilActual = (new Participante())->buscarPorUsuarioId((int) $_SESSION['usuario_id']);
            $usuarioActual = (new Usuario())->buscarPorId((int) $_SESSION['usuario_id']);
            if (!$perfilActual || !$usuarioActual) {
                throw new \RuntimeException('La cuenta no tiene un perfil de participante asociado.');
            }
            $datos['nombre'] = $usuarioActual['nombre'];
            $datos['apellido'] = $usuarioActual['apellido'];
            $datos['correo'] = $usuarioActual['correo'];
            $datos['telefono'] = $usuarioActual['telefono'] ?? '';
        }

        $errores = Validaciones::validar([
            fn() => !$actividad ? 'Actividad no encontrada.' : null,
            fn() => $actividad && !$actividadModelo->admiteInscripcion($actividad) ? 'Esta actividad no admite inscripciones en su estado actual.' : null,
            fn() => Validaciones::requerido($datos['nombre'], 'nombre'),
            fn() => Validaciones::requerido($datos['apellido'], 'apellido'),
            fn() => Validaciones::email($datos['correo']),
            fn() => Validaciones::rangoNumerico((float) $datos['edad'], 5, 100, 'edad'),
            fn() => $actividad && !empty($actividad['edad_minima']) && $datos['edad'] < $actividad['edad_minima']
                ? 'La edad minima para esta actividad es ' . $actividad['edad_minima'] . ' años.' : null,
            fn() => $actividad && !empty($actividad['edad_maxima']) && $datos['edad'] > $actividad['edad_maxima']
                ? 'La edad maxima para esta actividad es ' . $actividad['edad_maxima'] . ' años.' : null,
            fn() => $actividad && $actividadModelo->cuposOcupados($actividadId) >= (int) $actividad['cupos_disponibles']
                ? 'El cupo maximo de la actividad ha sido alcanzado.' : null,
        ]);

        if (!empty($errores)) {
            $this->flashErrors($errores, $datos);
            $this->redirect('/inscripciones/individual/crear?actividad_id=' . $actividadId);
        }

        $participante = $perfilActual
            ? ['participante_id' => (int) $perfilActual['id'], 'usuario_id' => (int) $_SESSION['usuario_id']]
            : (new Participante())->encontrarOCrear($datos['nombre'], $datos['apellido'], $datos['correo'], $datos['telefono']);

        $inscripcionModelo = new InscripcionIndividual();
        if ($inscripcionModelo->yaInscrito($actividadId, $participante['participante_id'])) {
            $this->flashErrors(['Ya existe una inscripcion con este correo para esta actividad.'], $datos);
            $this->redirect('/inscripciones/individual/crear?actividad_id=' . $actividadId);
        }

        $inscripcionId = $inscripcionModelo->crear($actividadId, $participante['participante_id']);

        $pagoId = (new Pago())->registrar([
            'inscripcion_individual_id' => $inscripcionId,
            'participante_id' => $participante['participante_id'],
            'metodo_pago' => 'EFECTIVO',
            'monto' => $actividad['costo_inscripcion'],
        ]);

        $facturaId = (new Factura())->emitir([
            'pago_id' => $pagoId,
            'participante_id' => $participante['participante_id'],
            'actividad_id' => $actividadId,
            'nombre_cliente' => $datos['nombre'] . ' ' . $datos['apellido'],
            'correo_cliente' => $datos['correo'],
            'subtotal' => $actividad['costo_inscripcion'],
            'concepto' => 'Inscripción individual a ' . $actividad['nombre'],
        ], (int) $participante['usuario_id']);

        (new Bitacora())->registrar(
            $esParticipante ? (int) $_SESSION['usuario_id'] : null,
            'INSCRIPCIONES', 'CREAR_INDIVIDUAL', 'inscripciones_individuales', (string) $inscripcionId,
            "Inscripcion publica de {$datos['correo']} a la actividad #{$actividadId}."
        );

        $this->redirect('/factura-publica?id=' . $facturaId);
    }
}
