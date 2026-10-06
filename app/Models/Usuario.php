<?php
namespace App\Models;

use App\Core\Model;

final class Usuario extends Model
{
    public static function all(): array
    {
        return static::rows('SELECT * FROM usuarios ORDER BY activo DESC, rol, usuario');
    }

    public static function find(int $id): ?array
    {
        return static::row('SELECT * FROM usuarios WHERE id = ?', [$id]);
    }

    public static function findByLogin(string $login): ?array
    {
        return static::row('SELECT * FROM usuarios WHERE usuario = ?', [$login]);
    }

    public static function create(array $d): int
    {
        static::exec('INSERT INTO usuarios (usuario, nombre, clave_hash, rol) VALUES (?, ?, ?, ?)', [$d['usuario'], $d['nombre'], $d['clave_hash'], $d['rol']]);
        return static::lastId();
    }

    public static function update(int $id, array $d): void
    {
        static::exec('UPDATE usuarios SET usuario = ?, nombre = ?, rol = ?, activo = ? WHERE id = ?', [$d['usuario'], $d['nombre'], $d['rol'], (int) $d['activo'], $id]);
    }

    public static function setPassword(int $id, string $hash): void
    {
        static::exec('UPDATE usuarios SET clave_hash = ? WHERE id = ?', [$hash, $id]);
    }
}
