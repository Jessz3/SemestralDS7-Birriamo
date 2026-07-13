<?php

declare(strict_types=1);

namespace App\Security;

/**
 * Implementacion del contrato de seguridad para firma de integridad
 * de registros usando HMAC-SHA256. Se utiliza para detectar
 * manipulacion fuera de banda (tampering) en registros criticos:
 * actividades, inscripciones, facturas, estadisticas, etc.
 *
 * La cadena canonica del registro se firma al guardar y se
 * vuelve a calcular al momento de generar reportes/auditorias
 * para verificar que el registro no fue alterado directamente en BD.
 */
final class FirmaDigitalHmacService implements TransformadorSeguridadInterface
{
    private string $llaveMaestra;

    public function __construct(?string $llaveMaestra = null)
    {
        // En produccion, esta llave debe residir en una variable de entorno
        // fuera del codigo fuente y del repositorio (ver documento Key Management).
        $this->llaveMaestra = $llaveMaestra ?? (getenv('APP_HMAC_KEY') ?: 'CAMBIAR_ESTA_LLAVE_EN_PRODUCCION_2026');
    }

    public function proteger(string $dato, ?string $llave = null): string
    {
        return hash_hmac('sha256', $dato, $llave ?? $this->llaveMaestra);
    }

    public function verificar(string $dato, string $sello, ?string $llave = null): bool
    {
        $calculado = $this->proteger($dato, $llave);
        return hash_equals($calculado, $sello);
    }

    /**
     * Construye la cadena canonica a partir de un arreglo ordenado de campos.
     * Todos los modelos deben usar este mismo formato para firmar y verificar.
     */
    public static function cadenaCanonica(array $campos): string
    {
        return implode('|', array_map(
            static fn($v) => is_bool($v) ? ($v ? '1' : '0') : (string) $v,
            $campos
        ));
    }
}
