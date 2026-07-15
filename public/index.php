<?php

declare(strict_types=1);

// ------------------------------------------------------------------
// Front Controller - Punto de entrada unico de la aplicacion.
// ------------------------------------------------------------------

error_reporting(E_ALL);
ini_set('display_errors', '0'); // En produccion no se muestran errores crudos al usuario.

session_start();

date_default_timezone_set('America/Panama');

define('BASE_URL', '');
define('ROOT_PATH', dirname(__DIR__));

require ROOT_PATH . '/vendor/autoload.php';

use App\Config\Env;
use App\Core\Router;

Env::cargar(ROOT_PATH . '/.env');

// Manejo centralizado de errores (Requisito #12: control de errores en todo el proyecto).
set_exception_handler(static function (Throwable $e): void {
    error_log('[EXCEPCION] ' . $e->getMessage() . ' en ' . $e->getFile() . ':' . $e->getLine());
    http_response_code(500);
    require ROOT_PATH . '/views/errors/500.php';
});

set_error_handler(static function (int $severity, string $message, string $file, int $line): bool {
    error_log("[ERROR PHP] {$message} en {$file}:{$line}");
    return true;
});

$router = new Router();

// ---------- Autenticacion ----------
$router->get('/login', 'AuthController', 'mostrarLogin');
$router->post('/login', 'AuthController', 'login');
$router->get('/logout', 'AuthController', 'logout');
$router->get('/registro', 'UsuarioController', 'registroForm');
$router->post('/registro', 'UsuarioController', 'registrar');

// ---------- Dashboard ----------
$router->get('/dashboard', 'HomeController', 'dashboard');

// ---------- Usuarios (Requisito #2) ----------
$router->get('/usuarios', 'UsuarioController', 'index');
$router->get('/usuarios/crear', 'UsuarioController', 'crearForm');
$router->post('/usuarios/crear', 'UsuarioController', 'crear');
$router->get('/usuarios/editar', 'UsuarioController', 'editarForm');
$router->post('/usuarios/editar', 'UsuarioController', 'actualizar');
$router->get('/usuarios/deshabilitar', 'UsuarioController', 'deshabilitar');
$router->get('/usuarios/habilitar', 'UsuarioController', 'habilitar');
$router->get('/mi-cuenta/password', 'UsuarioController', 'cambiarPasswordForm');
$router->post('/mi-cuenta/password', 'UsuarioController', 'cambiarPassword');

// ---------- Deportes (Requisito #3) ----------
$router->get('/deportes', 'DeporteController', 'index');
$router->get('/deportes/crear', 'DeporteController', 'crearForm');
$router->post('/deportes/crear', 'DeporteController', 'crear');
$router->get('/deportes/editar', 'DeporteController', 'editarForm');
$router->post('/deportes/editar', 'DeporteController', 'actualizar');
$router->get('/deportes/deshabilitar', 'DeporteController', 'deshabilitar');
$router->get('/deportes/habilitar', 'DeporteController', 'habilitar');

// ---------- Instalaciones (Requisito #4) ----------
$router->get('/instalaciones', 'InstalacionController', 'index');
$router->get('/instalaciones/crear', 'InstalacionController', 'crearForm');
$router->post('/instalaciones/crear', 'InstalacionController', 'crear');
$router->get('/instalaciones/editar', 'InstalacionController', 'editarForm');
$router->post('/instalaciones/editar', 'InstalacionController', 'actualizar');
$router->get('/instalaciones/deshabilitar', 'InstalacionController', 'deshabilitar');
$router->get('/instalaciones/habilitar', 'InstalacionController', 'habilitar');

// ---------- Academias y Organizadores (Requisito #5) ----------
$router->get('/academias', 'AcademiaController', 'index');
$router->get('/academias/crear', 'AcademiaController', 'crearForm');
$router->post('/academias/crear', 'AcademiaController', 'crear');
$router->get('/academias/editar', 'AcademiaController', 'editarForm');
$router->post('/academias/editar', 'AcademiaController', 'actualizar');
$router->get('/academias/deshabilitar', 'AcademiaController', 'deshabilitar');
$router->get('/academias/habilitar', 'AcademiaController', 'habilitar');

$router->get('/organizadores', 'OrganizadorController', 'index');
$router->get('/organizadores/crear', 'OrganizadorController', 'crearForm');
$router->post('/organizadores/crear', 'OrganizadorController', 'crear');
$router->get('/organizadores/editar', 'OrganizadorController', 'editarForm');
$router->post('/organizadores/editar', 'OrganizadorController', 'actualizar');
$router->post('/organizadores/verificar', 'OrganizadorController', 'verificar');
$router->post('/organizadores/deshabilitar', 'OrganizadorController', 'deshabilitar');
$router->post('/organizadores/habilitar', 'OrganizadorController', 'habilitar');

// ---------- Entrenadores ----------
$router->get('/entrenadores', 'EntrenadorController', 'index');
$router->get('/entrenadores/crear', 'EntrenadorController', 'crearForm');
$router->post('/entrenadores/crear', 'EntrenadorController', 'crear');

// ---------- Arbitros (Requisito #19) ----------
$router->get('/arbitros', 'ArbitroController', 'index');
$router->get('/arbitros/crear', 'ArbitroController', 'crearForm');
$router->post('/arbitros/crear', 'ArbitroController', 'crear');
$router->get('/arbitros/evaluar', 'ArbitroController', 'evaluarForm');
$router->post('/arbitros/evaluar', 'ArbitroController', 'evaluar');

// ---------- Equipos (Requisito #7) ----------
$router->get('/equipos', 'EquipoController', 'index');
$router->get('/equipos/crear', 'EquipoController', 'crearForm');
$router->post('/equipos/crear', 'EquipoController', 'crear');
$router->get('/equipos/ver', 'EquipoController', 'ver');
$router->post('/equipos/jugadores/agregar', 'EquipoController', 'agregarJugador');
$router->get('/equipos/jugadores/eliminar', 'EquipoController', 'eliminarJugador');

// ---------- Actividades (Requisitos #9 y #10) ----------
$router->get('/actividades', 'ActividadController', 'index');
$router->get('/actividades/crear', 'ActividadController', 'crearForm');
$router->post('/actividades/crear', 'ActividadController', 'crear');
$router->get('/actividades/editar', 'ActividadController', 'editarForm');
$router->post('/actividades/editar', 'ActividadController', 'actualizar');
$router->get('/actividades/ver', 'ActividadController', 'ver');
$router->get('/actividades/publicar', 'ActividadController', 'publicar');
$router->get('/actividades/cerrar-inscripciones', 'ActividadController', 'cerrarInscripciones');
$router->get('/actividades/finalizar', 'ActividadController', 'finalizar');
$router->get('/actividades/cancelar', 'ActividadController', 'cancelarForm');
$router->post('/actividades/cancelar', 'ActividadController', 'cancelar');
$router->get('/actividades/incidente/crear', 'ActividadController', 'reportarIncidenteForm');
$router->post('/actividades/incidente/crear', 'ActividadController', 'reportarIncidente');

// ---------- Inscripciones (Requisitos #7 y #8) ----------
$router->get('/inscripciones/equipo/crear', 'InscripcionController', 'inscribirEquipoForm');
$router->post('/inscripciones/equipo/crear', 'InscripcionController', 'inscribirEquipo');
$router->get('/inscripciones/equipo/aprobar', 'InscripcionController', 'aprobarEquipo');
$router->get('/inscripciones/equipo/rechazar', 'InscripcionController', 'rechazarEquipo');
$router->get('/inscripciones/individual/crear', 'InscripcionController', 'inscribirIndividualForm');
$router->post('/inscripciones/individual/crear', 'InscripcionController', 'inscribirIndividual');

// ---------- Facturas (Requisito #18) ----------
$router->get('/facturas', 'FacturaController', 'index');
$router->get('/facturas/ver', 'FacturaController', 'ver');
$router->get('/facturas/descargar', 'FacturaController', 'descargarPdf');
$router->get('/factura-publica', 'FacturaController', 'verPublica');

// ---------- Estadisticas (Requisito #19) ----------
$router->get('/estadisticas', 'EstadisticaController', 'index');

// ---------- Configuracion y mensajes de contacto ----------
$router->get('/configuracion', 'ConfiguracionController', 'index');
$router->post('/configuracion/actualizar', 'ConfiguracionController', 'actualizar');
$router->get('/configuracion/mensajes', 'ConfiguracionController', 'mensajes');
$router->get('/configuracion/mensajes/leido', 'ConfiguracionController', 'marcarLeido');

// ---------- Pagina publica ----------
$router->get('/', 'PublicController', 'inicio');
$router->post('/contacto', 'PublicController', 'contacto');

$requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';

// El participante usa un portal acotado. Esta lista tambien impide acceder
// manualmente por URL a modulos administrativos que no aparecen en su menu.
if (($_SESSION['usuario_rol'] ?? '') === 'PARTICIPANTE') {
    $rutasParticipante = [
        '/', '/dashboard', '/logout', '/actividades', '/actividades/ver',
        '/equipos', '/equipos/crear', '/equipos/ver',
        '/equipos/jugadores/agregar', '/equipos/jugadores/eliminar',
        '/inscripciones/equipo/crear', '/inscripciones/individual/crear',
        '/facturas', '/facturas/ver', '/facturas/descargar',
        '/factura-publica', '/mi-cuenta/password',
    ];

    if (!in_array($requestUri, $rutasParticipante, true)
        && !preg_match('#^/evento/[a-f0-9]{64}$#i', $requestUri)) {
        http_response_code(403);
        ob_start();
        require ROOT_PATH . '/views/errors/403.php';
        $content = ob_get_clean();
        require ROOT_PATH . '/views/layout/main.php';
        exit;
    }
}

// Ruta dinamica /evento/{token} generada por el codigo QR de cada actividad
// (token_publico: 64 caracteres hexadecimales).
if (preg_match('#^/evento/([a-f0-9]{64})$#i', $requestUri, $coincidencia)) {
    (new \App\Controllers\PublicController())->evento($coincidencia[1]);
    exit;
}

$router->dispatch($_SERVER['REQUEST_METHOD'], $requestUri);
