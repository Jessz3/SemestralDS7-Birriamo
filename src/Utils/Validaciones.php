<?php

declare(strict_types=1);

namespace App\Utils;

/**
 * Clase estatica utilitaria para validar datos de entrada.
 * Cada metodo retorna string|null: null si es valido, o el mensaje de error.
 */
final class Validaciones
{
    private function __construct()
    {
    }

    public static function requerido(mixed $valor, string $campo): ?string
    {
        if ($valor === null || trim((string) $valor) === '') {
            return "El campo {$campo} es obligatorio.";
        }
        return null;
    }

    public static function longitud(string $valor, int $min, int $max, string $campo): ?string
    {
        $len = mb_strlen($valor);
        if ($len < $min || $len > $max) {
            return "El campo {$campo} debe tener entre {$min} y {$max} caracteres.";
        }
        return null;
    }

    public static function email(string $valor): ?string
    {
        if (!filter_var($valor, FILTER_VALIDATE_EMAIL)) {
            return 'El correo electronico no tiene un formato valido.';
        }
        return null;
    }

    public static function numerico(mixed $valor, string $campo): ?string
    {
        if (!is_numeric($valor)) {
            return "El campo {$campo} debe ser numerico.";
        }
        return null;
    }

    public static function entero(mixed $valor, string $campo): ?string
    {
        if (filter_var($valor, FILTER_VALIDATE_INT) === false) {
            return "El campo {$campo} debe ser un numero entero.";
        }
        return null;
    }

    public static function rangoNumerico(float $valor, float $min, float $max, string $campo): ?string
    {
        if ($valor < $min || $valor > $max) {
            return "El campo {$campo} debe estar entre {$min} y {$max}.";
        }
        return null;
    }

    public static function fecha(string $valor, string $formato = 'Y-m-d'): ?string
    {
        $dt = \DateTime::createFromFormat($formato, $valor);
        if (!$dt || $dt->format($formato) !== $valor) {
            return 'La fecha proporcionada no es valida.';
        }
        return null;
    }

    public static function fechaFutura(string $valor, string $formato = 'Y-m-d'): ?string
    {
        $dt = \DateTime::createFromFormat($formato, $valor);
        if (!$dt || $dt < new \DateTime('today')) {
            return 'La fecha debe ser igual o posterior a hoy.';
        }
        return null;
    }

    public static function passwordSegura(string $valor): ?string
    {
        if (mb_strlen($valor) < 8) {
            return 'La contraseña debe tener al menos 8 caracteres.';
        }
        if (mb_strlen($valor) > 12) {
            return 'La contraseña debe tener como maximo 12 caracteres.';
        }
        if (!preg_match('/[A-Z]/', $valor) || !preg_match('/[0-9]/', $valor)) {
            return 'La contraseña debe incluir al menos una mayuscula y un numero.';
        }
        return null;
    }

    public static function enLista(mixed $valor, array $lista, string $campo): ?string
    {
        if (!in_array($valor, $lista, true)) {
            return "El valor del campo {$campo} no es valido.";
        }
        return null;
    }

    /**
     * Ejecuta un conjunto de validaciones (closures) y retorna todos los
     * mensajes de error encontrados. Permite componer reglas por campo.
     *
     * @param array<int, callable> $reglas
     * @return array<int, string>
     */
    public static function validar(array $reglas): array
    {
        $errores = [];
        foreach ($reglas as $regla) {
            $resultado = $regla();
            if ($resultado !== null) {
                $errores[] = $resultado;
            }
        }
        return $errores;
    }

    public static function nombrePersona(string $valor, string $campo): ?string
    {
        if (!preg_match('/^[A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]+$/u', $valor)) {
            return "El campo {$campo} solo puede contener letras y espacios.";
        }

        return null;
    }
}
