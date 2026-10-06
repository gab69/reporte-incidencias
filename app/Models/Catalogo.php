<?php
namespace App\Models;

use App\Core\Model;

/** Base de los catálogos (plataformas y tipos): se editan y desactivan, nunca se eliminan. */
abstract class Catalogo extends Model
{
    abstract protected static function table(): string;

    /** Columnas editables además de "activo". */
    protected static function columns(): array
    {
        return ['nombre'];
    }

    public static function all(): array
    {
        return static::rows('SELECT * FROM ' . static::table() . ' ORDER BY nombre');
    }

    public static function activos(): array
    {
        return static::rows('SELECT * FROM ' . static::table() . ' WHERE activo = 1 ORDER BY nombre');
    }

    /** Activos, más el elemento actual aunque esté desactivado (para editar). */
    public static function activosOActual(int $currentId): array
    {
        return static::rows('SELECT * FROM ' . static::table() . ' WHERE activo = 1 OR id = ? ORDER BY nombre', [$currentId]);
    }

    public static function nombre(int $id): ?string
    {
        return static::scalar('SELECT nombre FROM ' . static::table() . ' WHERE id = ?', [$id]) ?: null;
    }

    public static function crear(array $d): int
    {
        $cols = static::columns();
        static::exec(
            'INSERT INTO ' . static::table() . ' (' . implode(',', $cols) . ') VALUES (' . implode(',', array_fill(0, count($cols), '?')) . ')',
            array_map(fn($c) => $d[$c], $cols)
        );
        return static::lastId();
    }

    public static function actualizar(int $id, array $d): void
    {
        $cols = static::columns();
        $set = implode(',', array_map(fn($c) => "$c = ?", array_merge($cols, ['activo'])));
        $values = array_merge(array_map(fn($c) => $d[$c], $cols), [(int) $d['activo'], $id]);
        static::exec('UPDATE ' . static::table() . " SET $set WHERE id = ?", $values);
    }
}
