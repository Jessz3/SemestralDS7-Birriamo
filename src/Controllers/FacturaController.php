<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Factura;
use App\Models\Participante;
use TCPDF;

/**
 * Requisito #18: Facturacion con ITBMS configurable, generada con TCPDF
 * y firmada digitalmente (HMAC-SHA256), con hash SHA-256 del PDF final
 * como capa adicional de integridad (pdf_hash_sha256).
 */
final class FacturaController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();
        $modelo = new Factura();
        $esParticipante = $this->esParticipante();
        $this->render('facturas/index', [
            'facturas' => $esParticipante
                ? $modelo->porParticipante($this->participanteIdActual())
                : $modelo->todas(),
            'esParticipante' => $esParticipante,
        ]);
    }

    public function ver(): void
    {
        $this->requireAuth();
        $this->mostrarFactura((int) ($_GET['id'] ?? 0), 'facturas/ver', 'layout/main', true);
    }

    public function verPublica(): void
    {
        $this->mostrarFactura((int) ($_GET['id'] ?? 0), 'facturas/ver_publica', 'layout/guest');
    }

    private function mostrarFactura(int $id, string $vista, string $layout = 'layout/main', bool $validarAcceso = false): void
    {
        $modelo = new Factura();
        $factura = $modelo->buscarPorId($id);

        if (!$factura) {
            $this->redirect('/');
        }

        if ($validarAcceso) {
            $this->autorizarFactura($factura);
        }

        $this->render($vista, [
            'factura' => $factura,
            'integra' => $modelo->verificarIntegridad($factura),
        ], $layout);
    }

    public function descargarPdf(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $modelo = new Factura();
        $factura = $modelo->buscarPorId($id);

        if (!$factura) {
            http_response_code(404);
            echo 'Factura no encontrada.';
            return;
        }

        if (!empty($_SESSION['usuario_id'])) {
            $this->autorizarFactura($factura);
        }

        if (!class_exists(TCPDF::class)) {
            http_response_code(500);
            echo 'TCPDF no esta instalado. Ejecute "composer require tecnickcom/tcpdf" en el proyecto.';
            return;
        }

        $integra = $modelo->verificarIntegridad($factura);

        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator('Sistema de Eventos Deportivos');
        $pdf->SetAuthor('Universidad Tecnologica de Panama');
        $pdf->SetTitle('Factura ' . $factura['numero_factura']);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(15, 15, 15);
        $pdf->AddPage();

        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'Sistema de Eventos Deportivos', 0, 1, 'C');
        $pdf->SetFont('helvetica', '', 11);
        $pdf->Cell(0, 8, 'Factura No. ' . $factura['numero_factura'], 0, 1, 'C');
        $pdf->Ln(6);

        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(0, 6, 'Fecha de emision: ' . $factura['fecha_venta'], 0, 1);
        $pdf->Cell(0, 6, 'Cliente: ' . $factura['nombre_cliente'], 0, 1);
        $pdf->Cell(0, 6, 'Actividad: ' . $factura['actividad_nombre'], 0, 1);
        $pdf->Cell(0, 6, 'Tipo de actividad: ' . $factura['actividad_tipo'], 0, 1);
        $pdf->Cell(0, 6, 'Fecha de la actividad: ' . $factura['actividad_fecha'], 0, 1);
        $pdf->Ln(4);

        $filas = '';
        foreach ($factura['detalles'] as $detalle) {
            $filas .= '<tr><td>' . htmlspecialchars($detalle['descripcion']) . '</td>'
                . '<td>' . number_format((float) $detalle['cantidad'], 2) . '</td>'
                . '<td>$' . number_format((float) $detalle['precio_unitario'], 2) . '</td>'
                . '<td>$' . number_format((float) $detalle['subtotal_linea'], 2) . '</td></tr>';
        }

        $html = '<table border="1" cellpadding="6" style="font-size:10px;">
            <tr style="background-color:#f0f0f0;"><th>Descripcion</th><th>Cant.</th><th>Precio</th><th>Subtotal</th></tr>'
            . $filas .
            '<tr><td colspan="3" style="text-align:right;">Subtotal</td><td>$' . number_format((float) $factura['subtotal'], 2) . '</td></tr>
            <tr><td colspan="3" style="text-align:right;">ITBMS (' . number_format((float) $factura['tasa_itbms'], 2) . '%)</td><td>$' . number_format((float) $factura['itbms'], 2) . '</td></tr>
            <tr style="font-weight:bold;"><td colspan="3" style="text-align:right;">Total</td><td>$' . number_format((float) $factura['total'], 2) . '</td></tr>
        </table>';
        $pdf->writeHTML($html, true, false, true, false, '');

        $pdf->Ln(6);
        $pdf->SetFont('helvetica', 'I', 8);
        $pdf->MultiCell(0, 5,
            "Firma digital ({$factura['algoritmo_firma']}) del contenido de la factura:\n" . $factura['firma_digital'] .
            "\n\nEstado de integridad al momento de la descarga: " . ($integra ? 'VALIDA - el registro no ha sido alterado' : 'ADVERTENCIA - el registro pudo haber sido alterado'),
            0, 'L');

        $pdf->Ln(4);
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(0, 5, 'Documento ' . $factura['formato_documento'] . ' con validez de integridad verificable. Universidad Tecnologica de Panama.', 0, 1, 'C');

        $directorioSalida = __DIR__ . '/../../public/uploads';
        if (!is_dir($directorioSalida) && !mkdir($directorioSalida, 0775, true) && !is_dir($directorioSalida)) {
            http_response_code(500);
            echo 'No fue posible crear el directorio para guardar la factura PDF.';
            return;
        }

        $rutaSalida = $directorioSalida . '/factura_' . $factura['id'] . '.pdf';
        $pdf->Output($rutaSalida, 'F');

        $hash = hash_file('sha256', $rutaSalida);
        $modelo->actualizarRutaPdf((int) $factura['id'], '/uploads/factura_' . $factura['id'] . '.pdf', $hash);

        $pdf->Output('Factura_' . $factura['numero_factura'] . '.pdf', 'D');
    }

    private function esParticipante(): bool
    {
        return ($_SESSION['usuario_rol'] ?? '') === 'PARTICIPANTE';
    }

    private function participanteIdActual(): int
    {
        $participante = (new Participante())->buscarPorUsuarioId((int) ($_SESSION['usuario_id'] ?? 0));
        if (!$participante) {
            throw new \RuntimeException('La cuenta no tiene un perfil de participante asociado.');
        }
        return (int) $participante['id'];
    }

    private function autorizarFactura(array $factura): void
    {
        if (!$this->esParticipante()) {
            return;
        }
        if ((int) ($factura['participante_id'] ?? 0) !== $this->participanteIdActual()) {
            http_response_code(403);
            $this->render('errors/403');
            exit;
        }
    }
}
