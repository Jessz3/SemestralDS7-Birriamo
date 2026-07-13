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
        $opciones = [
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ];

        $archivoConfiguracion = self::resolverConfiguracionOpenSsl();
        if ($archivoConfiguracion !== null) {
            $opciones['config'] = $archivoConfiguracion;
        }

        $recurso = openssl_pkey_new($opciones);

        if ($recurso === false) {
            throw new \RuntimeException('No fue posible generar el par de llaves RSA.');
        }

        if (!openssl_pkey_export($recurso, $llavePrivada, null, $opciones)) {
            throw new \RuntimeException('No fue posible exportar la llave privada RSA.');
        }

        $detalles = openssl_pkey_get_details($recurso);
        if ($detalles === false || empty($detalles['key'])) {
            throw new \RuntimeException('No fue posible obtener la llave publica RSA.');
        }

        return [
            'publica' => $detalles['key'],
            'privada' => $llavePrivada,
            'huella' => hash('sha256', $detalles['key']),
        ];
    }

    /**
     * En instalaciones XAMPP/Windows PHP no siempre encuentra openssl.cnf.
     * Se conserva primero cualquier ruta configurada por el servidor y luego
     * se buscan las ubicaciones habituales relativas al ejecutable de PHP.
     */
    private static function resolverConfiguracionOpenSsl(): ?string
    {
        $configurada = getenv('OPENSSL_CONF');
        $directorioPhp = dirname(PHP_BINARY);
        $candidatas = [
            is_string($configurada) ? $configurada : '',
            $directorioPhp . '/extras/ssl/openssl.cnf',
            $directorioPhp . '/extras/openssl/openssl.cnf',
        ];

        foreach ($candidatas as $candidata) {
            if ($candidata !== '' && is_file($candidata) && is_readable($candidata)) {
                return $candidata;
            }
        }

        return null;
    }

    /**
     * Cifra la llave privada con AES-256-CBC usando la passphrase del usuario.
     * Esto evita que un administrador de BD pueda suplantar al usuario.
     */
    public static function cifrarLlavePrivada(string $llavePrivada, string $passphrase): string
    {
        $iv = random_bytes(16);
        $llaveDerivada = hash('sha256', $passphrase, true);
        $cifrado = openssl_encrypt($llavePrivada, 'aes-256-cbc', $llaveDerivada, 0, $iv);

        if ($cifrado === false) {
            throw new \RuntimeException('No fue posible cifrar la llave privada RSA.');
        }

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
