<?php
namespace App\Core;

use PDOStatement;

/** Base de los modelos: atajos para ejecutar consultas preparadas. */
abstract class Model
{
    protected static function exec(string $sql, array $params = []): PDOStatement
    {
        $stmt = Database::pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    protected static function rows(string $sql, array $params = []): array
    {
        return static::exec($sql, $params)->fetchAll();
    }

    protected static function row(string $sql, array $params = []): ?array
    {
        return static::exec($sql, $params)->fetch() ?: null;
    }

    protected static function scalar(string $sql, array $params = []): mixed
    {
        return static::exec($sql, $params)->fetchColumn();
    }

    protected static function lastId(): int
    {
        return (int) Database::pdo()->lastInsertId();
    }
}
