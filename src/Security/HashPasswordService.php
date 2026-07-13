<?php

declare(strict_types=1);

namespace App\Security;

/**
 * Implementacion del contrato de seguridad para hashing de contrasenas
 * usando el algoritmo BCRYPT nativo de PHP (password_hash / password_verify).
 */
final class HashPasswordService implements TransformadorSeguridadInterface
{
    public function proteger(string $dato, ?string $llave = null): string
    {
        return password_hash($dato, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    public function verificar(string $dato, string $sello, ?string $llave = null): bool
    {
        return password_verify($dato, $sello);
    }

    public function requiereRehash(string $sello): bool
    {
        return password_needs_rehash($sello, PASSWORD_BCRYPT, ['cost' => 12]);
    }
}
