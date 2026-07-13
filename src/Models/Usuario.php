<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Usuario extends Model
{
    /** Todos los roles que admite la tabla usuarios. */
    public const ROLES = ['ADMINISTRADOR', 'OPERADOR', 'ORGANIZADOR', 'PARTICIPANTE'];

    /** Roles disponibles para autorregistro desde la pagina publica. */
    public const ROLES_REGISTRO = ['ORGANIZADOR', 'PARTICIPANTE'];

    /** Roles gestionables desde el panel de Usuarios (staff interno). */
    public const ROLES_STAFF = ['ADMINISTRADOR', 'OPERADOR'];

    public function todos(): array
    {
        $stmt = $this->db->prepare('SELECT * FROM usuarios ORDER BY id DESC');
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM usuarios WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function buscarPorUsuario(string $usuario): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM usuarios WHERE usuario = :usuario LIMIT 1');
        $stmt->execute(['usuario' => $usuario]);
        return $stmt->fetch() ?: null;
    }

    public function buscarPorCorreo(string $correo): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM usuarios WHERE correo = :correo LIMIT 1');
        $stmt->execute(['correo' => $correo]);
        return $stmt->fetch() ?: null;
    }

    public function correoExiste(string $correo, ?int $ignorarId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM usuarios WHERE correo = :correo';
        $params = ['correo' => $correo];
        if ($ignorarId !== null) {
            $sql .= ' AND id != :id';
            $params['id'] = $ignorarId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function usuarioExiste(string $usuario, ?int $ignorarId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM usuarios WHERE usuario = :usuario';
        $params = ['usuario' => $usuario];
        if ($ignorarId !== null) {
            $sql .= ' AND id != :id';
            $params['id'] = $ignorarId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Crea un usuario. Usado tanto para staff (Administrador/Operador)
     * como internamente para generar cuentas de Organizador o Participante
     * cuando se registra un equipo, una inscripcion o un organizador.
     */
    public function crear(array $datos): int
    {
        $sql = 'INSERT INTO usuarios
                    (nombre, apellido, correo, telefono, usuario, password_hash, rol,
                     activo, requiere_cambio_password, creado_por)
                VALUES
                    (:nombre, :apellido, :correo, :telefono, :usuario, :password_hash, :rol,
                     :activo, :requiere_cambio_password, :creado_por)';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'nombre' => $datos['nombre'],
            'apellido' => $datos['apellido'],
            'correo' => $datos['correo'],
            'telefono' => $datos['telefono'] ?? null,
            'usuario' => $datos['usuario'],
            'password_hash' => $datos['password_hash'],
            'rol' => $datos['rol'],
            'activo' => $datos['activo'] ?? 1,
            'requiere_cambio_password' => $datos['requiere_cambio_password'] ?? 0,
            'creado_por' => $datos['creado_por'] ?? null,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function actualizar(int $id, array $datos): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE usuarios SET nombre = :nombre, apellido = :apellido, correo = :correo,
             telefono = :telefono, usuario = :usuario, rol = :rol WHERE id = :id'
        );
        return $stmt->execute([
            'nombre' => $datos['nombre'],
            'apellido' => $datos['apellido'],
            'correo' => $datos['correo'],
            'telefono' => $datos['telefono'] ?? null,
            'usuario' => $datos['usuario'],
            'rol' => $datos['rol'],
            'id' => $id,
        ]);
    }

    public function actualizarPassword(int $id, string $passwordHash): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE usuarios SET password_hash = :password_hash, requiere_cambio_password = 0 WHERE id = :id'
        );
        return $stmt->execute(['password_hash' => $passwordHash, 'id' => $id]);
    }

    public function cambiarEstado(int $id, bool $activo): bool
    {
        $stmt = $this->db->prepare('UPDATE usuarios SET activo = :activo WHERE id = :id');
        return $stmt->execute(['activo' => $activo ? 1 : 0, 'id' => $id]);
    }

    public function registrarIntentoFallido(int $id): void
    {
        $stmt = $this->db->prepare(
            'UPDATE usuarios SET intentos_fallidos = intentos_fallidos + 1,
             bloqueado_hasta = IF(intentos_fallidos + 1 >= 5, DATE_ADD(NOW(), INTERVAL 15 MINUTE), bloqueado_hasta)
             WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
    }

    public function reiniciarIntentosFallidos(int $id): void
    {
        $stmt = $this->db->prepare(
            'UPDATE usuarios SET intentos_fallidos = 0, bloqueado_hasta = NULL, ultimo_acceso = NOW() WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
    }

    public function nombreCompleto(array $usuario): string
    {
        return trim($usuario['nombre'] . ' ' . $usuario['apellido']);
    }
}
