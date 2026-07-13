<?php

declare(strict_types=1);

namespace App\Security;

/**
 * Implementacion del contrato de seguridad para firma digital con
 * llaves RSA (publica/privada), usada para garantizar el NO REPUDIO
 * de acciones realizadas por los usuarios (Requisito #2).
 *
 * La llave privada del usuario se cifra con AES-256 usando una
 * passphrase que solo el usuario conoce (ver Documento Key Management),
 * nunca se almacena en texto plano en la base de datos.
 */
final class FirmaDigitalRsaService implements TransformadorSeguridadInterface
{
    /**
     * Genera un par de llaves RSA (publica/privada) para un nuevo usuario,
     * junto con la huella digital (SHA-256) de la llave publica, usada
     * como identificador corto en claves_rsa_usuario.huella_publica.
     *
     * @return array{publica: string, privada: string, huella: string}
     */
    public static function generarParDeLlaves(): array
    {
        $recurso = openssl_pkey_new([
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);

        openssl_pkey_export($recurso, $llavePrivada);
        $detalles = openssl_pkey_get_details($recurso);

        return [
            'publica' => $detalles['key'],
            'privada' => $llavePrivada,
            'huella' => hash('sha256', $detalles['key']),
        ];
    }

    /**
     * Cifra la llave privada con AES-256-CBC usando la passphrase del usuario.
     * Esto evita que un administrador de BD pueda suplantar al usuario.
     */
    public static function cifrarLlavePrivada(string $llavePrivada, string $passphrase): string
    {
        $iv = openssl_random_pseudo_bytes(16);
        $llaveDerivada = hash('sha256', $passphrase, true);
        $cifrado = openssl_encrypt($llavePrivada, 'aes-256-cbc', $llaveDerivada, 0, $iv);

        return base64_encode($iv) . ':' . $cifrado;
    }

    public static function descifrarLlavePrivada(string $llaveCifrada, string $passphrase): ?string
    {
        [$ivB64, $cifrado] = array_pad(explode(':', $llaveCifrada, 2), 2, '');
        $iv = base64_decode($ivB64);
        $llaveDerivada = hash('sha256', $passphrase, true);
        $resultado = openssl_decrypt($cifrado, 'aes-256-cbc', $llaveDerivada, 0, $iv);

        return $resultado === false ? null : $resultado;
    }

    /**
     * Firma un dato con la llave privada (ya descifrada en memoria).
     */
    public function proteger(string $dato, ?string $llave = null): string
    {
        if ($llave === null) {
            throw new \InvalidArgumentException('Se requiere la llave privada para firmar.');
        }

        openssl_sign($dato, $firma, $llave, OPENSSL_ALGO_SHA256);
        return base64_encode($firma);
    }

    /**
     * Verifica una firma usando la llave publica del usuario.
     */
    public function verificar(string $dato, string $sello, ?string $llave = null): bool
    {
        if ($llave === null) {
            throw new \InvalidArgumentException('Se requiere la llave publica para verificar.');
        }

        $resultado = openssl_verify($dato, base64_decode($sello), $llave, OPENSSL_ALGO_SHA256);
        return $resultado === 1;
    }
}
