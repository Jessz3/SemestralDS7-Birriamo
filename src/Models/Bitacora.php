<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Security\FirmaDigitalHmacService;

/**
 * Bitacora de auditoria general del sistema.
 *
 * Reemplaza la firma de integridad por-registro que tenian las
 * actividades en la version anterior: ahora cada accion relevante
 * (crear/actualizar/cancelar actividad, aprobar inscripcion, etc.)
 * queda registrada aqui con su propia firma HMAC-SHA256, calculada
 * sobre modulo + accion + tabla + registro + datos nuevos. Esto
 * centraliza la trazabilidad y el no-repudio de operaciones.
 */
final class Bitacora extends Model
{
    private FirmaDigitalHmacService $firma;

    public function __construct()
    {
        parent::__construct();
        $this->firma = new FirmaDigitalHmacService();
    }

    /**
     * Registra un evento en la bitacora y calcula su firma digital.
     */
    public function registrar(
        ?int $usuarioId,
        string $modulo,
        string $accion,
        ?string $tablaAfectada = null,
        ?string $registroId = null,
        ?string $descripcion = null,
        ?array $datosAnteriores = null,
        ?array $datosNuevos = null
    ): int {
        $jsonAnteriores = $datosAnteriores !== null ? json_encode($datosAnteriores, JSON_UNESCAPED_UNICODE) : null;
        $jsonNuevos = $datosNuevos !== null ? json_encode($datosNuevos, JSON_UNESCAPED_UNICODE) : null;

        $cadena = FirmaDigitalHmacService::cadenaCanonica([
            $modulo, $accion, $tablaAfectada ?? '', $registroId ?? '', $jsonNuevos ?? '',
        ]);
        $firmaDigital = $this->firma->proteger($cadena);

        $stmt = $this->db->prepare(
            'INSERT INTO bitacora
                (usuario_id, modulo, accion, tabla_afectada, registro_id, descripcion,
                 datos_anteriores, datos_nuevos, direccion_ip, agente_usuario,
                 firma_digital, algoritmo_firma)
             VALUES
                (:usuario_id, :modulo, :accion, :tabla_afectada, :registro_id, :descripcion,
                 :datos_anteriores, :datos_nuevos, :direccion_ip, :agente_usuario,
                 :firma_digital, :algoritmo_firma)'
        );

        $stmt->execute([
            'usuario_id' => $usuarioId,
            'modulo' => $modulo,
            'accion' => $accion,
            'tabla_afectada' => $tablaAfectada,
            'registro_id' => $registroId,
            'descripcion' => $descripcion,
            'datos_anteriores' => $jsonAnteriores,
            'datos_nuevos' => $jsonNuevos,
            'direccion_ip' => $_SERVER['REMOTE_ADDR'] ?? null,
            'agente_usuario' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500) ?: null,
            'firma_digital' => $firmaDigital,
            'algoritmo_firma' => 'HMAC-SHA256',
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Verifica la integridad de una entrada de bitacora comparando
     * su firma almacenada contra una recalculada a partir de sus datos.
     */
    public function verificarIntegridad(array $entrada): bool
    {
        $cadena = FirmaDigitalHmacService::cadenaCanonica([
            $entrada['modulo'], $entrada['accion'], $entrada['tabla_afectada'] ?? '',
            $entrada['registro_id'] ?? '', $entrada['datos_nuevos'] ?? '',
        ]);
        return $this->firma->verificar($cadena, $entrada['firma_digital']);
    }

    public function porTablaYRegistro(string $tabla, string $registroId): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM bitacora WHERE tabla_afectada = :tabla AND registro_id = :registro_id ORDER BY fecha_evento DESC'
        );
        $stmt->execute(['tabla' => $tabla, 'registro_id' => $registroId]);
        return $stmt->fetchAll();
    }

    public function recientes(int $limite = 50): array
    {
        $stmt = $this->db->prepare(
            "SELECT b.*, CONCAT(u.nombre, ' ', u.apellido) AS usuario_nombre
             FROM bitacora b
             LEFT JOIN usuarios u ON u.id = b.usuario_id
             ORDER BY b.fecha_evento DESC
             LIMIT :limite"
        );
        $stmt->bindValue('limite', $limite, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
