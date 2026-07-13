<?php

declare(strict_types=1);

namespace App\Utils;

/**
 * Clase estatica utilitaria para sanitizar datos de entrada.
 * Cumple con recomendaciones OWASP contra XSS e inyeccion de datos.
 * Principio DRY: centraliza toda la logica de sanitizacion del sistema.
 */
final class Sanitizacion
{
    private function __construct()
    {
        // Clase estatica: no se permite instanciacion.
    }

    public static function texto(?string $valor): string
    {
        $valor = trim($valor ?? '');
        $valor = strip_tags($valor);
        return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
    }

    public static function textoPlano(?string $valor): string
    {
        return trim(strip_tags($valor ?? ''));
    }

    public static function email(?string $valor): string
    {
        $valor = trim($valor ?? '');
        return filter_var($valor, FILTER_SANITIZE_EMAIL) ?: '';
    }

    public static function entero(mixed $valor): int
    {
        return (int) filter_var($valor, FILTER_SANITIZE_NUMBER_INT);
    }

    public static function decimal(mixed $valor): float
    {
        $valor = filter_var($valor, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        return (float) $valor;
    }

    public static function alfanumerico(?string $valor): string
    {
        return preg_replace('/[^A-Za-z0-9\s]/', '', trim($valor ?? '')) ?? '';
    }

    public static function nombreArchivo(string $valor): string
    {
        $valor = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $valor) ?? 'archivo';
        return substr($valor, 0, 150);
    }

    /**
     * Sanitiza recursivamente un arreglo completo (por ejemplo $_POST).
     */
    public static function arreglo(array $datos): array
    {
        $resultado = [];
        foreach ($datos as $clave => $valor) {
            if (is_array($valor)) {
                $resultado[$clave] = self::arreglo($valor);
            } else {
                $resultado[$clave] = self::texto((string) $valor);
            }
        }
        return $resultado;
    }
}
