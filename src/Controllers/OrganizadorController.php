<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Academia;
use App\Models\Bitacora;
use App\Models\ClaveRsaUsuario;
use App\Models\Organizador;
use App\Models\Usuario;
use App\Security\FirmaDigitalRsaService;
use App\Security\HashPasswordService;
use App\Utils\Sanitizacion;
use App\Utils\Validaciones;

/**
 * Requisito #5: Modulo de Organizadores Deportivos.
 * En este esquema, todo organizador tiene una cuenta de usuario propia
 * (rol ORGANIZADOR) con su propio par de llaves RSA para firma digital
 * de no-repudio, igual que el staff administrativo.
 */
final class OrganizadorController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();
        $this->render('organizadores/index', [
            'organizadores' => (new Organizador())->todos(),
            'exito' => $this->getSuccess(),
        ]);
    }

    public function crearForm(): void
    {
        $this->requireAuth();
        $this->render('organizadores/crear', [
            'academias' => (new Academia())->todos(),
            'tipos' => Organizador::TIPOS,
            'errores' => $this->getErrors(),
            'datos' => $this->oldInput(),
            'csrf' => $_SESSION['csrf_token'],
        ]);
    }

    public function crear(): void
    {
        $this->requireAuth();
        $this->verifyCsrf();

        $datos = [
            'nombre' => Sanitizacion::texto($_POST['nombre'] ?? ''),
            'apellido' => Sanitizacion::texto($_POST['apellido'] ?? ''),
            'correo' => Sanitizacion::email($_POST['correo'] ?? ''),
            'telefono' => Sanitizacion::texto($_POST['telefono'] ?? ''),
            'tipo_organizador' => $_POST['tipo_organizador'] ?? 'INDEPENDIENTE',
            'nombre_comercial' => Sanitizacion::texto($_POST['nombre_comercial'] ?? ''),
            'descripcion' => Sanitizacion::texto($_POST['descripcion'] ?? ''),
            'academia_id' => Sanitizacion::entero($_POST['academia_id'] ?? 0) ?: null,
            'passphrase_llave' => (string) ($_POST['passphrase_llave'] ?? ''),
        ];

        $usuarioModelo = new Usuario();

        $errores = Validaciones::validar([
            fn() => Validaciones::requerido($datos['nombre'], 'nombre'),
            fn() => Validaciones::requerido($datos['apellido'], 'apellido'),
            fn() => Validaciones::requerido($datos['correo'], 'correo'),
            fn() => Validaciones::email($datos['correo']),
            fn() => Validaciones::enLista($datos['tipo_organizador'], Organizador::TIPOS, 'tipo de organizador'),
            fn() => Validaciones::requerido($datos['passphrase_llave'], 'frase de seguridad de la llave privada'),
            fn() => $usuarioModelo->correoExiste($datos['correo']) ? 'Ya existe un usuario con ese correo.' : null,
        ]);

        if (!empty($errores)) {
            $this->flashErrors($errores, $datos);
            $this->redirect('/organizadores/crear');
        }

        $hasher = new HashPasswordService();
        $usuarioSlug = strtolower(preg_replace('/[^a-z0-9]/i', '', $datos['nombre'] . '.' . $datos['apellido']));
        $sufijo = 1;
        $candidato = $usuarioSlug;
        while ($usuarioModelo->usuarioExiste($candidato)) {
            $candidato = $usuarioSlug . $sufijo++;
        }

        $usuarioId = $usuarioModelo->crear([
            'nombre' => $datos['nombre'],
            'apellido' => $datos['apellido'],
            'correo' => $datos['correo'],
            'telefono' => $datos['telefono'],
            'usuario' => $candidato,
            'password_hash' => $hasher->proteger(bin2hex(random_bytes(8))),
            'rol' => 'ORGANIZADOR',
            'requiere_cambio_password' => 1,
            'creado_por' => (int) $_SESSION['usuario_id'],
        ]);

        $par = FirmaDigitalRsaService::generarParDeLlaves();
        $llavePrivadaCifrada = FirmaDigitalRsaService::cifrarLlavePrivada($par['privada'], $datos['passphrase_llave']);
        (new ClaveRsaUsuario())->crear($usuarioId, $par['publica'], $llavePrivadaCifrada, $par['huella']);

        $organizadorId = (new Organizador())->crear($usuarioId, $datos);

        (new Bitacora())->registrar(
            (int) $_SESSION['usuario_id'],
            'ORGANIZADORES',
            'CREAR',
            'organizadores',
            (string) $organizadorId,
            "Registro de organizador {$datos['nombre']} {$datos['apellido']} (usuario: {$candidato})."
        );

        $this->flashSuccess("Organizador registrado. Usuario generado: {$candidato}.");
        $this->redirect('/organizadores');
    }

    public function editarForm(): void
    {
        $this->requireAuth();
        $organizador = (new Organizador())->buscarPorId((int) ($_GET['id'] ?? 0));
        if (!$organizador) {
            $this->redirect('/organizadores');
        }
        $this->render('organizadores/editar', [
            'organizador' => $organizador,
            'academias' => (new Academia())->todos(),
            'tipos' => Organizador::TIPOS,
            'errores' => $this->getErrors(),
            'csrf' => $_SESSION['csrf_token'],
        ]);
    }

    public function actualizar(): void
    {
        $this->requireAuth();
        $this->verifyCsrf();

        $id = (int) ($_POST['id'] ?? 0);
        $datos = [
            'tipo_organizador' => $_POST['tipo_organizador'] ?? 'INDEPENDIENTE',
            'nombre_comercial' => Sanitizacion::texto($_POST['nombre_comercial'] ?? ''),
            'descripcion' => Sanitizacion::texto($_POST['descripcion'] ?? ''),
            'academia_id' => Sanitizacion::entero($_POST['academia_id'] ?? 0) ?: null,
        ];

        (new Organizador())->actualizar($id, $datos);
        $this->flashSuccess('Organizador actualizado correctamente.');
        $this->redirect('/organizadores');
    }

    public function verificar(): void
    {
        $this->requireRole('ADMINISTRADOR');
        $this->verifyCsrf($_GET['csrf_token'] ?? null);
        $id = (int) ($_GET['id'] ?? 0);
        (new Organizador())->verificar($id, (int) $_SESSION['usuario_id']);
        $this->flashSuccess('Organizador verificado.');
        $this->redirect('/organizadores');
    }

    public function deshabilitar(): void
    {
        $this->requireAuth();
        $this->verifyCsrf($_GET['csrf_token'] ?? null);
        (new Organizador())->cambiarEstado((int) ($_GET['id'] ?? 0), false);
        $this->redirect('/organizadores');
    }

    public function habilitar(): void
    {
        $this->requireAuth();
        $this->verifyCsrf($_GET['csrf_token'] ?? null);
        (new Organizador())->cambiarEstado((int) ($_GET['id'] ?? 0), true);
        $this->redirect('/organizadores');
    }
}
