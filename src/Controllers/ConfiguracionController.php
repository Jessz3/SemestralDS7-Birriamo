<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Bitacora;
use App\Models\Configuracion;
use App\Models\MensajeContacto;
use App\Utils\Sanitizacion;

/** Requisito de configuracion global del sistema (tasa de ITBMS, moneda, etc.). */
final class ConfiguracionController extends Controller
{
    public function index(): void
    {
        $this->requireRole('ADMINISTRADOR');
        $this->render('configuracion/index', [
            'configuraciones' => (new Configuracion())->todas(),
            'exito' => $this->getSuccess(),
        ]);
    }

    public function actualizar(): void
    {
        $this->requireRole('ADMINISTRADOR');
        $this->verifyCsrf();

        $clave = Sanitizacion::alfanumerico($_POST['clave'] ?? '');
        $valor = Sanitizacion::texto($_POST['valor'] ?? '');

        (new Configuracion())->actualizar($clave, $valor);
        (new Bitacora())->registrar(
            (int) $_SESSION['usuario_id'], 'CONFIGURACION', 'ACTUALIZAR', 'configuracion_sistema', $clave,
            "Configuracion {$clave} actualizada a: {$valor}"
        );

        $this->flashSuccess('Configuracion actualizada.');
        $this->redirect('/configuracion');
    }

    public function mensajes(): void
    {
        $this->requireAuth();
        $this->render('configuracion/mensajes', [
            'mensajes' => (new MensajeContacto())->todos(),
        ]);
    }

    public function marcarLeido(): void
    {
        $this->requireAuth();
        $this->verifyCsrf($_GET['csrf_token'] ?? null);
        $id = (int) ($_GET['id'] ?? 0);
        (new MensajeContacto())->marcarLeido($id, (int) $_SESSION['usuario_id']);
        $this->redirect('/configuracion/mensajes');
    }
}
