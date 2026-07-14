<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Bitacora;
use App\Models\Usuario;
use App\Security\HashPasswordService;
use App\Utils\Sanitizacion;
use App\Utils\Validaciones;

/**
 * Requisito #1: Login.
 * Requisito #12: Control de errores en el login.
 */
final class AuthController extends Controller
{
    public function mostrarLogin(): void
    {
        if (!empty($_SESSION['usuario_id'])) {
            $this->redirect('/dashboard');
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $this->render('auth/login', [
            'errores' => $this->getErrors(),
            'exito' => $this->getSuccess(),
            'csrf' => $_SESSION['csrf_token'],
        ], 'layout/guest');
    }

    public function login(): void
    {
        $this->verifyCsrf();

        $usuarioInput = Sanitizacion::textoPlano($_POST['usuario'] ?? '');
        $password = (string) ($_POST['password'] ?? '');

        $errores = Validaciones::validar([
            fn() => Validaciones::requerido($usuarioInput, 'usuario'),
            fn() => Validaciones::requerido($password, 'contraseña'),
        ]);

        if (!empty($errores)) {
            $this->flashErrors($errores);
            $this->redirect('/login');
        }

        $modelo = new Usuario();
        $registro = $modelo->buscarPorUsuario($usuarioInput);

        // Mensaje generico intencional (no revelar si el usuario existe o no).
        $mensajeGenerico = 'Usuario o contraseña incorrectos.';

        if (!$registro) {
            $this->flashErrors([$mensajeGenerico]);
            $this->redirect('/login');
        }

        if (!empty($registro['bloqueado_hasta']) && strtotime($registro['bloqueado_hasta']) > time()) {
            $this->flashErrors(['Cuenta bloqueada temporalmente por multiples intentos fallidos. Intente en unos minutos.']);
            $this->redirect('/login');
        }

        if ((int) $registro['activo'] === 0) {
            $this->flashErrors(['Esta cuenta se encuentra deshabilitada. Contacte al administrador.']);
            $this->redirect('/login');
        }

        $hasher = new HashPasswordService();
        if (!$hasher->verificar($password, $registro['password_hash'])) {
            $modelo->registrarIntentoFallido((int) $registro['id']);
            $this->flashErrors([$mensajeGenerico]);
            $this->redirect('/login');
        }

        $modelo->reiniciarIntentosFallidos((int) $registro['id']);

        session_regenerate_id(true);
        $_SESSION['usuario_id'] = $registro['id'];
        $_SESSION['usuario_nombre'] = $modelo->nombreCompleto($registro);
        $_SESSION['usuario_rol'] = $registro['rol'];

        (new Bitacora())->registrar(
            (int) $registro['id'],
            'AUTENTICACION',
            'LOGIN',
            'usuarios',
            (string) $registro['id'],
            'Inicio de sesión exitoso.'
        );

        if ((int) $registro['requiere_cambio_password'] === 1) {
            $this->redirect('/mi-cuenta/password');
        }

        $this->redirect('/dashboard');
    }

    public function logout(): void
    {
        $_SESSION = [];
        session_destroy();
        session_start();
        $this->redirect('/login');
    }
}
