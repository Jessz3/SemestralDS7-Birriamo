<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Bitacora;
use App\Models\ClaveRsaUsuario;
use App\Models\HistorialPassword;
use App\Models\Usuario;
use App\Security\FirmaDigitalRsaService;
use App\Security\HashPasswordService;
use App\Utils\Sanitizacion;
use App\Utils\Validaciones;

/**
 * Requisito #2: CRUD de usuarios administrativos/operadores.
 * Solo el ADMINISTRADOR puede acceder a este modulo.
 */
final class UsuarioController extends Controller
{
    public function index(): void
    {
        $this->requireRole('ADMINISTRADOR');
        $modelo = new Usuario();
        $this->render('usuarios/index', [
            'usuarios' => $modelo->todosStaff(),
            'exito' => $this->getSuccess(),
        ]);
    }

    public function crearForm(): void
    {
        $this->requireRole('ADMINISTRADOR');
        $this->render('usuarios/crear', [
            'errores' => $this->getErrors(),
            'datos' => $this->oldInput(),
            'csrf' => $_SESSION['csrf_token'],
        ]);
    }

    public function crear(): void
    {
        $this->requireRole('ADMINISTRADOR');
        $this->verifyCsrf();

        $datos = [
            'nombre' => Sanitizacion::texto($_POST['nombre'] ?? ''),
            'apellido' => Sanitizacion::texto($_POST['apellido'] ?? ''),
            'correo' => Sanitizacion::email($_POST['correo'] ?? ''),
            'usuario' => Sanitizacion::alfanumerico($_POST['usuario'] ?? ''),
            'rol' => $_POST['rol'] ?? 'OPERADOR',
            'password' => (string) ($_POST['password'] ?? ''),
            'passphrase_llave' => (string) ($_POST['passphrase_llave'] ?? ''),
        ];

        $modelo = new Usuario();

        $errores = Validaciones::validar([
            fn() => Validaciones::requerido($datos['nombre'], 'nombre'),
            fn() => Validaciones::requerido($datos['apellido'], 'apellido'),
            fn() => Validaciones::requerido($datos['correo'], 'correo'),
            fn() => Validaciones::email($datos['correo']),
            fn() => Validaciones::requerido($datos['usuario'], 'usuario'),
            fn() => Validaciones::enLista($datos['rol'], Usuario::ROLES_STAFF, 'rol'),
            fn() => Validaciones::passwordSegura($datos['password']),
            fn() => Validaciones::requerido($datos['passphrase_llave'], 'frase de seguridad de la llave privada'),
            fn() => $modelo->correoExiste($datos['correo']) ? 'Ya existe un usuario con ese correo.' : null,
            fn() => $modelo->usuarioExiste($datos['usuario']) ? 'Ya existe ese nombre de usuario.' : null,
        ]);

        if (!empty($errores)) {
            $this->flashErrors($errores, $datos);
            $this->redirect('/usuarios/crear');
        }

        $hasher = new HashPasswordService();
        $usuarioId = $modelo->crear([
            'nombre' => $datos['nombre'],
            'apellido' => $datos['apellido'],
            'correo' => $datos['correo'],
            'usuario' => $datos['usuario'],
            'password_hash' => $hasher->proteger($datos['password']),
            'rol' => $datos['rol'],
            'creado_por' => (int) $_SESSION['usuario_id'],
        ]);

        $this->generarLlavesRsa($usuarioId, $datos['passphrase_llave']);

        (new Bitacora())->registrar(
            (int) $_SESSION['usuario_id'],
            'USUARIOS',
            'CREAR',
            'usuarios',
            (string) $usuarioId,
            "Creacion de usuario {$datos['usuario']} con rol {$datos['rol']}."
        );

        $this->flashSuccess('Usuario creado correctamente. Se genero su par de llaves RSA para firma digital.');
        $this->redirect('/usuarios');
    }

    private function generarLlavesRsa(int $usuarioId, string $passphrase): void
    {
        $par = FirmaDigitalRsaService::generarParDeLlaves();
        $llavePrivadaCifrada = FirmaDigitalRsaService::cifrarLlavePrivada($par['privada'], $passphrase);
        (new ClaveRsaUsuario())->crear($usuarioId, $par['publica'], $llavePrivadaCifrada, $par['huella']);
    }

    public function editarForm(): void
    {
        $this->requireRole('ADMINISTRADOR');
        $id = (int) ($_GET['id'] ?? 0);
        $modelo = new Usuario();
        $usuario = $modelo->buscarPorId($id);

        if (!$usuario) {
            $this->redirect('/usuarios');
        }

        $this->render('usuarios/editar', [
            'usuario' => $usuario,
            'errores' => $this->getErrors(),
            'csrf' => $_SESSION['csrf_token'],
        ]);
    }

    public function actualizar(): void
    {
        $this->requireRole('ADMINISTRADOR');
        $this->verifyCsrf();

        $id = (int) ($_POST['id'] ?? 0);
        $datos = [
            'nombre' => Sanitizacion::texto($_POST['nombre'] ?? ''),
            'apellido' => Sanitizacion::texto($_POST['apellido'] ?? ''),
            'correo' => Sanitizacion::email($_POST['correo'] ?? ''),
            'usuario' => Sanitizacion::alfanumerico($_POST['usuario'] ?? ''),
            'rol' => $_POST['rol'] ?? 'OPERADOR',
        ];

        $modelo = new Usuario();

        $errores = Validaciones::validar([
            fn() => Validaciones::requerido($datos['nombre'], 'nombre'),
            fn() => Validaciones::requerido($datos['apellido'], 'apellido'),
            fn() => Validaciones::email($datos['correo']),
            fn() => Validaciones::enLista($datos['rol'], Usuario::ROLES_STAFF, 'rol'),
            fn() => $modelo->correoExiste($datos['correo'], $id) ? 'Ya existe otro usuario con ese correo.' : null,
            fn() => $modelo->usuarioExiste($datos['usuario'], $id) ? 'Ya existe otro usuario con ese nombre de usuario.' : null,
        ]);

        if (!empty($errores)) {
            $this->flashErrors($errores);
            $this->redirect('/usuarios/editar?id=' . $id);
        }

        $modelo->actualizar($id, $datos);
        $this->flashSuccess('Usuario actualizado correctamente.');
        $this->redirect('/usuarios');
    }

    public function deshabilitar(): void
    {
        $this->requireRole('ADMINISTRADOR');
        $id = (int) ($_GET['id'] ?? 0);
        (new Usuario())->cambiarEstado($id, false);
        $this->flashSuccess('Usuario deshabilitado.');
        $this->redirect('/usuarios');
    }

    public function habilitar(): void
    {
        $this->requireRole('ADMINISTRADOR');
        $id = (int) ($_GET['id'] ?? 0);
        (new Usuario())->cambiarEstado($id, true);
        $this->flashSuccess('Usuario habilitado.');
        $this->redirect('/usuarios');
    }

    public function cambiarPasswordForm(): void
    {
        $this->requireAuth();
        $this->render('usuarios/cambiar_password', [
            'errores' => $this->getErrors(),
            'csrf' => $_SESSION['csrf_token'],
        ]);
    }

    public function cambiarPassword(): void
    {
        $this->requireAuth();
        $this->verifyCsrf();

        $actual = (string) ($_POST['password_actual'] ?? '');
        $nueva = (string) ($_POST['password_nueva'] ?? '');
        $confirmacion = (string) ($_POST['password_confirmacion'] ?? '');

        $modelo = new Usuario();
        $usuario = $modelo->buscarPorId((int) $_SESSION['usuario_id']);
        $hasher = new HashPasswordService();

        $errores = Validaciones::validar([
            fn() => !$hasher->verificar($actual, $usuario['password_hash']) ? 'La contrasena actual es incorrecta.' : null,
            fn() => Validaciones::passwordSegura($nueva),
            fn() => $nueva !== $confirmacion ? 'La confirmacion no coincide con la nueva contrasena.' : null,
        ]);

        if (!empty($errores)) {
            $this->flashErrors($errores);
            $this->redirect('/mi-cuenta/password');
        }

        $nuevoHash = $hasher->proteger($nueva);
        $modelo->actualizarPassword((int) $usuario['id'], $nuevoHash);
        (new HistorialPassword())->registrar((int) $usuario['id'], $nuevoHash);

        $this->flashSuccess('Contrasena actualizada correctamente.');
        $this->redirect('/dashboard');
    }
}
