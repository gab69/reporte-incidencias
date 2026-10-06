<?php
namespace App\Core;

use PDO;

/** Conexión PDO única (crea la base de datos si no existe). */
final class Database
{
    private static ?PDO $pdo = null;

    public static function pdo(): PDO
    {
        if (self::$pdo) {
            return self::$pdo;
        }
        $c = Config::get('db');
        $pdo = new PDO("mysql:host={$c['host']};charset={$c['charset']}", $c['user'], $c['pass'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        try {
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$c['name']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        } catch (\Throwable) {
            // hostings sin permiso para crear bases: se asume que ya existe
        }
        $pdo->exec("USE `{$c['name']}`");
        return self::$pdo = $pdo;
    }
}
