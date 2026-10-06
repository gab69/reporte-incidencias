<?php
namespace App\Models;

use App\Core\Model;

/** Configuración visual de la plataforma (nombre, colores, logo, favicon). */
final class Ajuste extends Model
{
    public const DEFAULTS = [
        'app_name'       => 'Centro de Incidencias',
        'color_primario' => '#1d4ed8',
        'color_menu'     => '#0f1b2d',
        'logo'           => '',
        'favicon'        => '',
    ];

    private static ?array $cache = null;

    public static function all(): array
    {
        if (self::$cache === null) {
            $saved = [];
            foreach (static::rows('SELECT clave, valor FROM ajustes') as $row) {
                $saved[$row['clave']] = $row['valor'];
            }
            self::$cache = array_merge(self::DEFAULTS, $saved);
        }
        return self::$cache;
    }

    public static function get(string $key): string
    {
        return (string) (self::all()[$key] ?? '');
    }

    public static function set(string $key, string $value): void
    {
        static::exec('INSERT INTO ajustes (clave, valor) VALUES (?, ?) ON DUPLICATE KEY UPDATE valor = VALUES(valor)', [$key, $value]);
        self::$cache = null;
    }
}
