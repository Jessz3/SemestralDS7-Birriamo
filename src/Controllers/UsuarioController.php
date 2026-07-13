<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Config\Database;
use App\Core\Controller;
use App\Models\Bitacora;
use App\Models\ClaveRsaUsuario;
use App\Models\HistorialPassword;
use App\Models\Organizador;
use App\Models\Participante;
use App\Models\Usuario;
use App\Security\FirmaDigitalRsaService;
use App\Security\HashPasswordService;
use App\Utils\Sanitizacion;
use App\Utils\Validaciones;
use PDOException;
use Throwable;

/**
 * Requisito #2: CRUD central de usuarios del sistema.
 * Solo el ADMINISTRADOR puede acceder a este modulo.
 */
final class UsuarioController extends Controller
{
    public function index(): void
    {
        $this->requireRole('ADMINISTRADOR');
        $modelo = new Usuario();
        $this->render('usuarios/index', [
            'usuarios' => $modelo->todos(),
            'errores' => $this->getErrors(),
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
            'rolesPermitidos' => Usuario::ROLES,
            'accion' => '/usuarios/crear',
            'cancelar' => '/usuarios',
            'titulo' => 'Nuevo Usuario',
            'eyebrow' => 'Administracion',
            'textoBoton' => 'Guardar usuario',
        ]);
    }

    public function crear(): void
    {
        $this->requireRole('ADMINISTRADOR');
        $this->verifyCsrf();

        $this->procesarCreacion(
            Usuario::ROLES,
            '/usuarios/crear',
            (int) $_SESSION['usuario_id']
        );

        $this->flashSuccess('Usuario creado correctamente. Se genero su par de llaves RSA para firma digital.');
        $this->redirect('/usuarios');
    }

    public function registroForm(): void
    {
        if (!empty($_SESSION['usuario_id'])) {
            $this->redirect('/dashboard');
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $this->render('usuarios/crear', [
            'errores' => $this->getErrors(),
            'datos' => $this->oldInput(),
            'csrf' => $_SESSION['csrf_token'],
            'rolesPermitidos' => Usuario::ROLES_REGISTRO,
            'accion' => '/registro',
            'cancelar' => '/login',
            'titulo' => 'Crear una cuenta',
            'eyebrow' => 'Registro',
            'textoBoton' => 'Registrarme',
        ], 'layout/guest');
    }

    public function registrar(): void
    {
        if (!empty($_SESSION['usuario_id'])) {
            $this->redirect('/dashboard');
        }

        $this->verifyCsrf();
        $this->procesarCreacion(Usuario::ROLES_REGISTRO, '/registro', null);

        $this->flashSuccess('Cuenta creada correctamente. Ya puede iniciar sesion.');
        $this->redirect('/login');
    }

    /**
     * Flujo compartido por el alta administrativa y el autorregistro.
     * Los roles permitidos se validan en servidor, no solo en el formulario.
     */
    private function procesarCreacion(array $rolesPermitidos, string $rutaError, ?int $creadoPor): void
    {

        $datos = [
            'nombre' => Sanitizacion::texto($_POST['nombre'] ?? ''),
            'apellido' => Sanitizacion::texto($_POST['apellido'] ?? ''),
            'correo' => Sanitizacion::email($_POST['correo'] ?? ''),
            'usuario' => Sanitizacion::alfanumerico($_POST['usuario'] ?? ''),
            'rol' => $_POST['rol'] ?? $rolesPermitidos[0],
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
            fn() => Validaciones::enLista($datos['rol'], $rolesPermitidos, 'rol'),
            fn() => Validaciones::passwordSegura($datos['password']),
            fn() => Validaciones::requerido($datos['passphrase_llave'], 'frase de seguridad de la llave privada'),
            fn() => $modelo->correoExiste($datos['correo']) ? 'Ya existe un usuario con ese correo.' : null,
            fn() => $modelo->usuarioExiste($datos['usuario']) ? 'Ya existe ese nombre de usuario.' : null,
        ]);

        if (!empty($errores)) {
            $this->flashErrors($errores, $this->datosReutilizables($datos));
            $this->redirect($rutaError);
        }

        $db = Database::getInstance()->getConnection();

        try {
            $db->beginTransaction();

            $hasher = new HashPasswordService();
            $usuarioId = $modelo->crear([
                'nombre' => $datos['nombre'],
                'apellido' => $datos['apellido'],
                'correo' => $datos['correo'],
                'usuario' => $datos['usuario'],
                'password_hash' => $hasher->proteger($datos['password']),
                'rol' => $datos['rol'],
                'creado_por' => $creadoPor,
            ]);

            $this->generarLlavesRsa($usuarioId, $datos['passphrase_llave']);
            $this->crearPerfilSegunRol($usuarioId, $datos['rol']);

            (new Bitacora())->registrar(
                $creadoPor ?? $usuarioId,
                'USUARIOS',
                $creadoPor === null ? 'AUTORREGISTRO' : 'CREAR',
                'usuarios',
                (string) $usuarioId,
                "Creacion de usuario {$datos['usuario']} con rol {$datos['rol']}."
            );

            $db->commit();
        } catch (Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }

            error_log('[USUARIOS] Error al crear usuario: ' . $e->getMessage());
            $mensaje = $e instanceof PDOException && $e->getCode() === '23000'
                ? 'El correo, nombre de usuario o cedula ya pertenece a otra cuenta.'
                : 'No se pudo crear el usuario. No se guardo ningun dato; intente nuevamente.';
            $this->flashErrors([$mensaje], $this->datosReutilizables($datos));
            $this->redirect($rutaError);
        }
    }

    private function generarLlavesRsa(int $usuarioId, string $passphrase): void
    {
        $par = FirmaDigitalRsaService::generarParDeLlaves();
        $llavePrivadaCifrada = FirmaDigitalRsaService::cifrarLlavePrivada($par['privada'], $passphrase);
        (new ClaveRsaUsuario())->crear($usuarioId, $par['publica'], $llavePrivadaCifrada, $par['huella']);
    }

    private function crearPerfilSegunRol(int $usuarioId, string $rol): void
    {
        if ($rol === 'ORGANIZADOR') {
            (new Organizador())->crear($usuarioId, [
                'academia_id' => null,
                'tipo_organizador' => 'INDEPENDIENTE',
                'nombre_comercial' => null,
                'descripcion' => null,
            ]);
        }

        if ($rol === 'PARTICIPANTE') {
            (new Participante())->crearPerfil($usuarioId);
        }
    }

    /** Evita conservar contrasenas o frases privadas en la sesion flash. */
    private function datosReutilizables(array $datos): array
    {
        unset($datos['password'], $datos['passphrase_llave']);
        return $datos;
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
        $usuarioActual = $modelo->buscarPorId($id);

        if (!$usuarioActual) {
            $this->flashErrors(['El usuario indicado no existe.']);
            $this->redirect('/usuarios');
        }

        $errores = Validaciones::validar([
            fn() => Validaciones::requerido($datos['nombre'], 'nombre'),
            fn() => Validaciones::requerido($datos['apellido'], 'apellido'),
            fn() => Validaciones::email($datos['correo']),
            fn() => Validaciones::enLista($datos['rol'], Usuario::ROLES, 'rol'),
            fn() => $datos['rol'] !== $usuarioActual['rol']
                && (!in_array($datos['rol'], Usuario::ROLES_STAFF, true)
                    || !in_array($usuarioActual['rol'], Usuario::ROLES_STAFF, true))
                    ? 'El rol Organizador o Participante no puede cambiarse porque tiene un perfil funcional asociado.'
                    : null,
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
