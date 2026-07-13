<?php

declare(strict_types=1);

namespace App\Config;

/**
 * Cargador simple de variables de entorno desde un archivo .env.
 * Evita depender de una libreria externa (vlucas/phpdotenv) para
 * un caso de uso tan pequeno, manteniendo cero dependencias extra.
 *
 * Formato soportado: CLAVE=valor (una por linea), comentarios con #.
 */
final class Env
{
    private static bool $cargado = false;

    public static function cargar(string $rutaArchivo): void
    {
        if (self::$cargado || !file_exists($rutaArchivo)) {
            self::$cargado = true;
            return;
        }

        $lineas = file($rutaArchivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lineas as $linea) {
            $linea = trim($linea);

            if ($linea === '' || str_starts_with($linea, '#')) {
                continue;
            }

            if (!str_contains($linea, '=')) {
                continue;
            }

            [$clave, $valor] = explode('=', $linea, 2);
            $clave = trim($clave);
            $valor = trim($valor, " \t\n\r\0\x0B\"'");

            // No sobrescribe variables ya definidas a nivel de servidor/sistema.
            if (getenv($clave) === false) {
                putenv("{$clave}={$valor}");
                $_ENV[$clave] = $valor;
            }
        }

        self::$cargado = true;
    }

    public static function get(string $clave, ?string $porDefecto = null): ?string
    {
        $valor = getenv($clave);
        return $valor !== false ? $valor : $porDefecto;
    }
}
