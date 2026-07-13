<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Actividad;
use App\Models\Arbitro;
use App\Models\Bitacora;
use App\Models\Deporte;
use App\Models\Devolucion;
use App\Models\Entrenador;
use App\Models\Equipo;
use App\Models\Factura;
use App\Models\Incidente;
use App\Models\InscripcionEquipo;
use App\Models\InscripcionIndividual;
use App\Models\Instalacion;
use App\Models\Organizador;
use App\Models\Pago;
use App\Models\Participante;
use App\Utils\Sanitizacion;
use App\Utils\Validaciones;

/**
 * Requisito #9: CRUD de Actividades Deportivas.
 * Requisito #10: Codigo QR por actividad (token_publico -> /evento/{token}).
 */
final class ActividadController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();

        if (($_SESSION['usuario_rol'] ?? '') === 'PARTICIPANTE') {
            $this->render('actividades/participante', [
                'actividades' => (new Actividad())->vigentesParaParticipante(),
                'exito' => $this->getSuccess(),
            ]);
            return;
        }

        $this->render('actividades/index', [
            'actividades' => (new Actividad())->todos(),
            'exito' => $this->getSuccess(),
        ]);
    }

    private function datosFormulario(): array
    {
        return [
            'deportes' => (new Deporte())->todos(true),
            'instalaciones' => (new Instalacion())->todos(true),
            'organizadores' => (new Organizador())->todos(),
            'entrenadores' => (new Entrenador())->todos(),
            'arbitros' => (new Arbitro())->todos(),
        ];
    }

    public function crearForm(): void
    {
        $this->requireAuth();
        $this->render('actividades/crear', array_merge($this->datosFormulario(), [
            'errores' => $this->getErrors(),
            'datos' => $this->oldInput(),
            'csrf' => $_SESSION['csrf_token'],
        ]));
    }

    private function recolectar(): array
    {
        return [
            'organizador_id' => Sanitizacion::entero($_POST['organizador_id'] ?? 0),
            'deporte_id' => Sanitizacion::entero($_POST['deporte_id'] ?? 0),
            'instalacion_id' => Sanitizacion::entero($_POST['instalacion_id'] ?? 0),
            'entrenador_id' => Sanitizacion::entero($_POST['entrenador_id'] ?? 0) ?: null,
            'tipo' => $_POST['tipo'] ?? 'EVENTO',
            'modalidad' => $_POST['modalidad'] ?? 'EQUIPO',
            'nombre' => Sanitizacion::texto($_POST['nombre'] ?? ''),
            'descripcion' => Sanitizacion::texto($_POST['descripcion'] ?? ''),
            'reglas' => Sanitizacion::texto($_POST['reglas'] ?? ''),
            'fecha_inicio' => str_replace('T', ' ', $_POST['fecha_inicio'] ?? ''),
            'fecha_fin' => str_replace('T', ' ', $_POST['fecha_fin'] ?? ''),
            'fecha_cierre_inscripcion' => !empty($_POST['fecha_cierre_inscripcion'])
                ? str_replace('T', ' ', $_POST['fecha_cierre_inscripcion']) : null,
            'edad_minima' => Sanitizacion::entero($_POST['edad_minima'] ?? 0) ?: null,
            'edad_maxima' => Sanitizacion::entero($_POST['edad_maxima'] ?? 0) ?: null,
            'cupos_disponibles' => Sanitizacion::entero($_POST['cupos_disponibles'] ?? 20),
            'capacidad_invitados' => Sanitizacion::entero($_POST['capacidad_invitados'] ?? 0),
            'requiere_pago' => isset($_POST['requiere_pago']) ? 1 : 0,
            'costo_inscripcion' => Sanitizacion::decimal($_POST['costo_inscripcion'] ?? 0),
            'costo_instalacion' => Sanitizacion::decimal($_POST['costo_instalacion'] ?? 0),
            'arbitros' => array_map('intval', $_POST['arbitros'] ?? []),
        ];
    }

    private function validar(array $datos): array
    {
        return Validaciones::validar([
            fn() => Validaciones::requerido($datos['nombre'], 'nombre'),
            fn() => Validaciones::requerido($datos['descripcion'], 'descripcion'),
            fn() => Validaciones::enLista($datos['tipo'], Actividad::TIPOS, 'tipo'),
            fn() => Validaciones::enLista($datos['modalidad'], Actividad::MODALIDADES, 'modalidad'),
            fn() => Validaciones::entero($datos['organizador_id'], 'organizador'),
            fn() => Validaciones::entero($datos['deporte_id'], 'deporte'),
            fn() => Validaciones::entero($datos['instalacion_id'], 'instalacion'),
            fn() => Validaciones::requerido($datos['fecha_inicio'], 'fecha de inicio'),
            fn() => Validaciones::requerido($datos['fecha_fin'], 'fecha de fin'),
            fn() => $datos['fecha_fin'] <= $datos['fecha_inicio'] ? 'La fecha de fin debe ser posterior a la fecha de inicio.' : null,
            fn() => Validaciones::rangoNumerico((float) $datos['cupos_disponibles'], 1, 100000, 'cupos disponibles'),
            fn() => Validaciones::rangoNumerico($datos['costo_inscripcion'], 0, 1000000, 'costo de inscripcion'),
        ]);
    }

    public function crear(): void
    {
        $this->requireAuth();
        $this->verifyCsrf();

        $datos = $this->recolectar();
        $errores = $this->validar($datos);

        if (!empty($errores)) {
            $this->flashErrors($errores, $datos);
            $this->redirect('/actividades/crear');
        }

        $organizadorId = $datos['organizador_id'];
        $actividadId = (new Actividad())->crear($datos, $organizadorId);

        foreach ($datos['arbitros'] as $arbitroId) {
            (new Arbitro())->asignarAActividad($actividadId, $arbitroId);
        }

        (new Bitacora())->registrar(
            (int) $_SESSION['usuario_id'],
            'ACTIVIDADES',
            'CREAR',
            'actividades',
            (string) $actividadId,
            "Actividad '{$datos['nombre']}' creada en estado BORRADOR.",
            null,
            $datos
        );

        $this->flashSuccess('Actividad creada correctamente en estado BORRADOR. Publicala para que sea visible.');
        $this->redirect('/actividades/ver?id=' . $actividadId);
    }

    public function editarForm(): void
    {
        $this->requireAuth();
        $actividad = (new Actividad())->buscarPorId((int) ($_GET['id'] ?? 0));
        if (!$actividad) {
            $this->redirect('/actividades');
        }
        $this->render('actividades/editar', array_merge($this->datosFormulario(), [
            'actividad' => $actividad,
            'errores' => $this->getErrors(),
            'csrf' => $_SESSION['csrf_token'],
        ]));
    }

    public function actualizar(): void
    {
        $this->requireAuth();
        $this->verifyCsrf();

        $id = (int) ($_POST['id'] ?? 0);
        $datos = $this->recolectar();
        $errores = $this->validar($datos);

        if (!empty($errores)) {
            $this->flashErrors($errores);
            $this->redirect('/actividades/editar?id=' . $id);
        }

        (new Actividad())->actualizar($id, $datos);

        (new Bitacora())->registrar(
            (int) $_SESSION['usuario_id'],
            'ACTIVIDADES',
            'ACTUALIZAR',
            'actividades',
            (string) $id,
            "Actividad '{$datos['nombre']}' actualizada.",
            null,
            $datos
        );

        $this->flashSuccess('Actividad actualizada correctamente.');
        $this->redirect('/actividades/ver?id=' . $id);
    }

    public function ver(): void
    {
        $this->requireAuth();
        $modelo = new Actividad();
        $id = (int) ($_GET['id'] ?? 0);
        $actividad = $modelo->buscarPorId($id);

        if (!$actividad) {
            $this->redirect('/actividades');
        }

        if (($_SESSION['usuario_rol'] ?? '') === 'PARTICIPANTE') {
            if ($actividad['estado'] !== 'PUBLICADA' || strtotime($actividad['fecha_fin']) < time()) {
                $this->redirect('/actividades');
            }

            $participante = (new Participante())->buscarPorUsuarioId((int) $_SESSION['usuario_id']);
            if (!$participante) {
                throw new \RuntimeException('La cuenta no tiene un perfil de participante asociado.');
            }
            $participanteId = (int) $participante['id'];
            $inscripcionesIndividuales = (new InscripcionIndividual())->porParticipante($participanteId);
            $inscripcionesEquipo = (new InscripcionEquipo())->porParticipante($participanteId);

            $this->render('actividades/ver_participante', [
                'actividad' => $actividad,
                'cuposOcupados' => $modelo->cuposOcupados($id),
                'equipos' => (new Equipo())->porParticipante($participanteId),
                'inscripcionIndividual' => array_values(array_filter(
                    $inscripcionesIndividuales,
                    static fn(array $i): bool => (int) $i['actividad_id'] === $id
                ))[0] ?? null,
                'inscripcionesEquipo' => array_values(array_filter(
                    $inscripcionesEquipo,
                    static fn(array $i): bool => (int) $i['actividad_id'] === $id
                )),
                'admiteInscripcion' => $modelo->admiteInscripcion($actividad),
                'exito' => $this->getSuccess(),
            ]);
            return;
        }

        $this->render('actividades/ver', [
            'actividad' => $actividad,
            'cuposOcupados' => $modelo->cuposOcupados($id),
            'arbitrosAsignados' => (new Arbitro())->arbitrosDeActividad($id),
            'inscripcionesEquipo' => (new InscripcionEquipo())->porActividad($id),
            'inscripcionesIndividual' => (new InscripcionIndividual())->porActividad($id),
            'incidentes' => (new Incidente())->porActividad($id),
            'urlPublica' => BASE_URL . '/evento/' . $actividad['token_publico'],
            'exito' => $this->getSuccess(),
        ]);
    }

    public function publicar(): void
    {
        $this->requireAuth();
        $id = (int) ($_GET['id'] ?? 0);
        (new Actividad())->publicar($id);
        (new Bitacora())->registrar((int) $_SESSION['usuario_id'], 'ACTIVIDADES', 'PUBLICAR', 'actividades', (string) $id, 'Actividad publicada.');
        $this->flashSuccess('Actividad publicada. Ya es visible en la pagina publica.');
        $this->redirect('/actividades/ver?id=' . $id);
    }

    public function cerrarInscripciones(): void
    {
        $this->requireAuth();
        $id = (int) ($_GET['id'] ?? 0);
        (new Actividad())->cerrarInscripciones($id);
        $this->flashSuccess('Inscripciones cerradas.');
        $this->redirect('/actividades/ver?id=' . $id);
    }

    public function finalizar(): void
    {
        $this->requireAuth();
        $id = (int) ($_GET['id'] ?? 0);
        (new Actividad())->finalizar($id);
        (new Bitacora())->registrar((int) $_SESSION['usuario_id'], 'ACTIVIDADES', 'FINALIZAR', 'actividades', (string) $id, 'Actividad finalizada.');
        $this->redirect('/actividades/ver?id=' . $id);
    }

    public function cancelarForm(): void
    {
        $this->requireAuth();
        $id = (int) ($_GET['id'] ?? 0);
        $this->render('actividades/cancelar', [
            'actividadId' => $id,
            'csrf' => $_SESSION['csrf_token'],
        ]);
    }

    /**
     * Cancela la actividad y genera automaticamente una solicitud de
     * devolucion por cada pago aprobado asociado (Requisito #9).
     */
    public function cancelar(): void
    {
        $this->requireAuth();
        $this->verifyCsrf();

        $id = (int) ($_POST['id'] ?? 0);
        $motivo = Sanitizacion::texto($_POST['motivo'] ?? 'Cancelada por el organizador.');

        $actividadModelo = new Actividad();
        $devolucionModelo = new Devolucion();
        $pagoModelo = new Pago();

        // Generar devoluciones para facturas ya emitidas de esta actividad.
        $stmt = $this->obtenerFacturasDeActividad($id);
        foreach ($stmt as $factura) {
            $devolucionModelo->crear(
                (int) $factura['pago_id'],
                (int) $factura['id'],
                $id,
                $motivo,
                (float) $factura['total'],
                (int) $_SESSION['usuario_id']
            );
        }

        $actividadModelo->cancelar($id, $motivo);
        (new InscripcionEquipo())->cancelarPorActividad($id);
        (new InscripcionIndividual())->cancelarPorActividad($id);

        (new Bitacora())->registrar(
            (int) $_SESSION['usuario_id'],
            'ACTIVIDADES',
            'CANCELAR',
            'actividades',
            (string) $id,
            "Actividad cancelada. Motivo: {$motivo}"
        );

        $this->flashSuccess('Actividad cancelada. Se generaron las solicitudes de devolucion correspondientes.');
        $this->redirect('/actividades/ver?id=' . $id);
    }

    private function obtenerFacturasDeActividad(int $actividadId): array
    {
        $factura = new Factura();
        $todas = $factura->todas();
        return array_values(array_filter($todas, static fn($f) => (int) $f['actividad_id'] === $actividadId && $f['estado'] === 'EMITIDA'));
    }

    public function reportarIncidenteForm(): void
    {
        $this->requireAuth();
        $id = (int) ($_GET['id'] ?? 0);
        $this->render('actividades/reportar_incidente', [
            'actividadId' => $id,
            'tipos' => Incidente::TIPOS,
            'gravedades' => Incidente::GRAVEDADES,
            'csrf' => $_SESSION['csrf_token'],
        ]);
    }

    public function reportarIncidente(): void
    {
        $this->requireAuth();
        $this->verifyCsrf();

        $datos = [
            'actividad_id' => (int) ($_POST['actividad_id'] ?? 0),
            'tipo' => $_POST['tipo'] ?? 'OTRO',
            'gravedad' => $_POST['gravedad'] ?? 'LEVE',
            'descripcion' => Sanitizacion::texto($_POST['descripcion'] ?? ''),
            'fecha_incidente' => $_POST['fecha_incidente'] ?? null,
            'equipo_id' => Sanitizacion::entero($_POST['equipo_id'] ?? 0) ?: null,
        ];

        (new Incidente())->crear($datos, (int) $_SESSION['usuario_id']);
        $this->flashSuccess('Incidente registrado.');
        $this->redirect('/actividades/ver?id=' . $datos['actividad_id']);
    }
}
