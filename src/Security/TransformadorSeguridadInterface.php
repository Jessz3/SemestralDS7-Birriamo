<?php

declare(strict_types=1);

namespace App\Security;

/**
 * Contrato (Programacion por Interfaces) que unifica los servicios
 * criptograficos de la aplicacion. Tanto el hashing de contraseñas
 * como la firma digital de registros implementan este mismo contrato,
 * desacoplando la logica de negocio del algoritmo especifico usado.
 *
 * Requisito #15: Contratos (PDO) / Programacion por Interfaces.
 */
interface TransformadorSeguridadInterface
{
    /**
     * Transforma (protege/firma) un dato de entrada.
     * Para hashing de contraseñas, $llave se ignora.
     * Para firmas digitales, $llave es la llave privada o secreta.
     */
    public function proteger(string $dato, ?string $llave = null): string;

    /**
     * Verifica que un dato corresponda con su sello/hash/firma.
     */
    public function verificar(string $dato, string $sello, ?string $llave = null): bool;
}
