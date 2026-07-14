<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Security\HashPasswordService;

/**
 * Modelo de Participantes. Un participante SIEMPRE tiene una cuenta de
 * usuario asociada (rol PARTICIPANTE), segun el nuevo esquema de base
 * de datos. Este modelo ofrece un metodo de conveniencia para crear
 * (o reutilizar) esa cuenta a partir de datos minimos capturados en
 * formularios publicos (inscripcion individual, registro de equipo),
 * sin exigirle al usuario final pasar por un flujo de registro completo.
 */
final class Participante extends Model
{
    public function buscarPorId(int $id): ?array
    {
        $sql = "SELECT p.*, u.nombre, u.apellido, u.correo, u.telefono,
                    CONCAT(u.nombre, ' ', u.apellido) AS nombre_completo
                FROM participantes p
                JOIN usuarios u ON u.id = p.usuario_id
                WHERE p.id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function buscarPorUsuarioId(int $usuarioId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM participantes WHERE usuario_id = :usuario_id LIMIT 1');
        $stmt->execute(['usuario_id' => $usuarioId]);
        return $stmt->fetch() ?: null;
    }

    public function todos(): array
    {
        $sql = "SELECT p.*, CONCAT(u.nombre, ' ', u.apellido) AS nombre_completo, u.correo
                FROM participantes p
                JOIN usuarios u ON u.id = p.usuario_id
                ORDER BY u.nombre ASC";
        return $this->db->query($sql)->fetchAll();
    }

    /**
     * Busca un participante por correo; si no existe, crea la cuenta de
     * usuario (rol PARTICIPANTE, con contraseña generada aleatoriamente
     * ya que no inicia sesión desde este panel) y su perfil de participante.
     *
     * @return array{participante_id: int, usuario_id: int, creado: bool}
     */
    public function encontrarOCrear(string $nombre, string $apellido, string $correo, ?string $telefono = null): array
    {
        $usuarioModelo = new Usuario();
        $usuarioExistente = $usuarioModelo->buscarPorCorreo($correo);

        if ($usuarioExistente) {
            $participante = $this->buscarPorUsuarioId((int) $usuarioExistente['id']);
            if ($participante) {
                return [
                    'participante_id' => (int) $participante['id'],
                    'usuario_id' => (int) $usuarioExistente['id'],
                    'creado' => false,
                ];
            }
            $participanteId = $this->crearPerfil((int) $usuarioExistente['id']);
            return ['participante_id' => $participanteId, 'usuario_id' => (int) $usuarioExistente['id'], 'creado' => false];
        }

        $hasher = new HashPasswordService();
        $usuarioSlug = $this->generarNombreUsuario($nombre, $apellido);

        $usuarioId = $usuarioModelo->crear([
            'nombre' => $nombre,
            'apellido' => $apellido,
            'correo' => $correo,
            'telefono' => $telefono,
            'usuario' => $usuarioSlug,
            'password_hash' => $hasher->proteger(bin2hex(random_bytes(8))),
            'rol' => 'PARTICIPANTE',
            'activo' => 1,
            'requiere_cambio_password' => 1,
        ]);

        $participanteId = $this->crearPerfil($usuarioId);

        return ['participante_id' => $participanteId, 'usuario_id' => $usuarioId, 'creado' => true];
    }

    public function crearPerfil(int $usuarioId): int
    {
        $stmt = $this->db->prepare('INSERT INTO participantes (usuario_id) VALUES (:usuario_id)');
        $stmt->execute(['usuario_id' => $usuarioId]);
        return (int) $this->db->lastInsertId();
    }

    private function generarNombreUsuario(string $nombre, string $apellido): string
    {
        $usuarioModelo = new Usuario();
        $base = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $nombre . '.' . $apellido) ?? 'participante');
        $base = substr($base, 0, 45) ?: 'participante';
        $candidato = $base;
        $sufijo = 1;

        while ($usuarioModelo->usuarioExiste($candidato)) {
            $candidato = $base . $sufijo;
            $sufijo++;
        }

        return $candidato;
    }
}
