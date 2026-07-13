<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Instalacion extends Model
{
    public const TIPOS = ['CANCHA', 'GIMNASIO', 'PISCINA', 'ESTADIO', 'PISTA', 'SALON', 'OTRO'];

    public function todos(bool $soloActivas = false): array
    {
        $sql = 'SELECT * FROM instalaciones';
        if ($soloActivas) {
            $sql .= ' WHERE activo = 1';
        }
        $sql .= ' ORDER BY nombre ASC';
        return $this->db->query($sql)->fetchAll();
    }

    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM instalaciones WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function crear(array $datos): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO instalaciones
                (nombre, tipo, descripcion, direccion, provincia, distrito, corregimiento,
                 espacio_disponible, capacidad_invitados, costo_base)
             VALUES
                (:nombre, :tipo, :descripcion, :direccion, :provincia, :distrito, :corregimiento,
                 :espacio_disponible, :capacidad_invitados, :costo_base)'
        );
        $stmt->execute($this->parametros($datos));
        return (int) $this->db->lastInsertId();
    }

    public function actualizar(int $id, array $datos): bool
    {
        $params = $this->parametros($datos);
        $params['id'] = $id;

        $stmt = $this->db->prepare(
            'UPDATE instalaciones SET nombre = :nombre, tipo = :tipo, descripcion = :descripcion,
             direccion = :direccion, provincia = :provincia, distrito = :distrito, corregimiento = :corregimiento,
             espacio_disponible = :espacio_disponible, capacidad_invitados = :capacidad_invitados,
             costo_base = :costo_base WHERE id = :id'
        );
        return $stmt->execute($params);
    }

    private function parametros(array $datos): array
    {
        return [
            'nombre' => $datos['nombre'],
            'tipo' => $datos['tipo'],
            'descripcion' => $datos['descripcion'] ?? null,
            'direccion' => $datos['direccion'],
            'provincia' => $datos['provincia'] ?: 'Panamá',
            'distrito' => $datos['distrito'] ?? null,
            'corregimiento' => $datos['corregimiento'] ?? null,
            'espacio_disponible' => $datos['espacio_disponible'] ?? null,
            'capacidad_invitados' => $datos['capacidad_invitados'] ?? 0,
            'costo_base' => $datos['costo_base'] ?? 0,
        ];
    }

    public function cambiarEstado(int $id, bool $activo): bool
    {
        $stmt = $this->db->prepare('UPDATE instalaciones SET activo = :activo WHERE id = :id');
        return $stmt->execute(['activo' => $activo ? 1 : 0, 'id' => $id]);
    }
}
