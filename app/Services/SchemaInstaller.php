<?php
namespace App\Services;

use App\Core\Database;
use PDO;

/** Crea (o actualiza desde versiones anteriores) las tablas de la base de datos. */
final class SchemaInstaller
{
    public static function ensure(): void
    {
        $pdo = Database::pdo();
        if (!self::tableExists($pdo, 'usuarios')) {
            self::install($pdo);
        } elseif (!self::tableExists($pdo, 'tipos')) {
            self::upgradeFromV1($pdo);
        }
        if (!self::columnExists($pdo, 'usuarios', 'activo')) {
            $pdo->exec('ALTER TABLE usuarios ADD activo TINYINT(1) NOT NULL DEFAULT 1');
        }
        if (!self::tableExists($pdo, 'auditoria')) {
            $pdo->exec('CREATE TABLE auditoria (id INT AUTO_INCREMENT PRIMARY KEY, usuario_id INT NULL, usuario_nombre VARCHAR(100) NOT NULL, accion VARCHAR(60) NOT NULL, entidad VARCHAR(20) NOT NULL, entidad_id INT NULL, detalle VARCHAR(500), fecha DATETIME DEFAULT CURRENT_TIMESTAMP, INDEX (usuario_nombre), INDEX (fecha))');
        }
        if (!self::tableExists($pdo, 'ajustes')) {
            $pdo->exec('CREATE TABLE ajustes (clave VARCHAR(50) PRIMARY KEY, valor TEXT)');
        }
    }

    private static function install(PDO $pdo): void
    {
        $pdo->exec("CREATE TABLE plataformas (id INT AUTO_INCREMENT PRIMARY KEY, nombre VARCHAR(60) NOT NULL UNIQUE, color VARCHAR(7) NOT NULL DEFAULT '#475569', activo TINYINT(1) NOT NULL DEFAULT 1)");
        $pdo->exec("INSERT INTO plataformas (nombre, color) VALUES ('Moodle','#f98012'),('Zoom','#2d8cff'),('Clementina','#0e9f6e'),('Blackboard','#334155'),('Trilce','#7c3aed')");
        $pdo->exec('CREATE TABLE tipos (id INT AUTO_INCREMENT PRIMARY KEY, nombre VARCHAR(60) NOT NULL UNIQUE, activo TINYINT(1) NOT NULL DEFAULT 1)');
        $pdo->exec("INSERT INTO tipos (nombre) VALUES ('Acceso'),('Problema de plataforma')");
        $pdo->exec("CREATE TABLE usuarios (id INT AUTO_INCREMENT PRIMARY KEY, usuario VARCHAR(50) NOT NULL UNIQUE, nombre VARCHAR(100) NOT NULL, clave_hash VARCHAR(255) NOT NULL, rol ENUM('admin','tecnico') NOT NULL DEFAULT 'tecnico', activo TINYINT(1) NOT NULL DEFAULT 1)");
        $pdo->exec('CREATE TABLE incidencias (id INT AUTO_INCREMENT PRIMARY KEY, plataforma_id INT NOT NULL, tipo_id INT NOT NULL, titulo VARCHAR(200) NOT NULL, descripcion TEXT, activo TINYINT(1) NOT NULL DEFAULT 1, creado_por INT NULL, creado_en DATETIME DEFAULT CURRENT_TIMESTAMP, actualizado_en DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, FOREIGN KEY (plataforma_id) REFERENCES plataformas(id), FOREIGN KEY (tipo_id) REFERENCES tipos(id), FOREIGN KEY (creado_por) REFERENCES usuarios(id) ON DELETE SET NULL)');
        $pdo->exec('CREATE TABLE pasos (id INT AUTO_INCREMENT PRIMARY KEY, incidencia_id INT NOT NULL, orden INT NOT NULL, texto TEXT, imagen VARCHAR(100) NULL, FOREIGN KEY (incidencia_id) REFERENCES incidencias(id) ON DELETE CASCADE)');
        $pdo->prepare("INSERT INTO usuarios (usuario, nombre, clave_hash, rol) VALUES ('admin', 'Administrador', ?, 'admin')")
            ->execute([password_hash('admin123', PASSWORD_DEFAULT)]);
    }

    /** La primera versión guardaba el tipo como ENUM y no tenía colores ni estado. */
    private static function upgradeFromV1(PDO $pdo): void
    {
        $pdo->exec('CREATE TABLE tipos (id INT AUTO_INCREMENT PRIMARY KEY, nombre VARCHAR(60) NOT NULL UNIQUE, activo TINYINT(1) NOT NULL DEFAULT 1)');
        $pdo->exec("INSERT INTO tipos (nombre) VALUES ('Acceso'),('Problema de plataforma')");
        $pdo->exec('ALTER TABLE incidencias ADD tipo_id INT NULL, ADD activo TINYINT(1) NOT NULL DEFAULT 1');
        $pdo->exec('UPDATE incidencias i JOIN tipos t ON t.nombre = i.tipo SET i.tipo_id = t.id');
        $pdo->exec('UPDATE incidencias SET tipo_id = (SELECT MIN(id) FROM tipos) WHERE tipo_id IS NULL');
        $pdo->exec('ALTER TABLE incidencias DROP COLUMN tipo');
        $pdo->exec("ALTER TABLE plataformas ADD color VARCHAR(7) NOT NULL DEFAULT '#475569', ADD activo TINYINT(1) NOT NULL DEFAULT 1");
        $colors = ['Moodle' => '#f98012', 'Zoom' => '#2d8cff', 'Clementina' => '#0e9f6e', 'Blackboard' => '#334155', 'Trilce' => '#7c3aed'];
        foreach ($colors as $name => $color) {
            $pdo->prepare('UPDATE plataformas SET color = ? WHERE nombre = ?')->execute([$color, $name]);
        }
    }

    private static function tableExists(PDO $pdo, string $table): bool
    {
        return (bool) $pdo->query("SHOW TABLES LIKE '$table'")->fetch();
    }

    private static function columnExists(PDO $pdo, string $table, string $column): bool
    {
        return (bool) $pdo->query("SHOW COLUMNS FROM `$table` LIKE '$column'")->fetch();
    }
}
