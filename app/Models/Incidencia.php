<?php
namespace App\Models;

use App\Core\Model;

final class Incidencia extends Model
{
    private const SELECT = 'SELECT i.*, p.nombre plat, p.color pcolor, t.nombre tipo, u.nombre autor,
            (SELECT COUNT(*) FROM pasos WHERE incidencia_id = i.id) np
        FROM incidencias i
        JOIN plataformas p ON p.id = i.plataforma_id
        JOIN tipos t ON t.id = i.tipo_id
        LEFT JOIN usuarios u ON u.id = i.creado_por';

    /**
     * Búsqueda con filtros: q (texto), p (plataforma), t (tipo), mias, inact.
     * Las desactivadas solo las ven su creador y los administradores.
     */
    public static function buscar(array $f, ?array $user, int $limit = 100): array
    {
        $where = [];
        $params = [];
        if ($f['inact'] && $user) {
            if ($user['rol'] === 'admin') {
                $where[] = '1 = 1';
            } else {
                $where[] = '(i.activo = 1 OR i.creado_por = ?)';
                $params[] = $user['id'];
            }
        } else {
            $where[] = 'i.activo = 1';
        }
        if ($f['mias'] && $user) {
            $where[] = 'i.creado_por = ?';
            $params[] = $user['id'];
        }
        if ($f['p']) {
            $where[] = 'i.plataforma_id = ?';
            $params[] = $f['p'];
        }
        if ($f['t']) {
            $where[] = 'i.tipo_id = ?';
            $params[] = $f['t'];
        }
        if ($f['q'] !== '') {
            $where[] = '(i.titulo LIKE ? OR i.descripcion LIKE ? OR EXISTS (SELECT 1 FROM pasos s WHERE s.incidencia_id = i.id AND s.texto LIKE ?))';
            $like = '%' . $f['q'] . '%';
            array_push($params, $like, $like, $like);
        }
        return static::rows(self::SELECT . ' WHERE ' . implode(' AND ', $where) . ' ORDER BY i.actualizado_en DESC LIMIT ' . (int) $limit, $params);
    }

    public static function find(int $id): ?array
    {
        return static::row(self::SELECT . ' WHERE i.id = ?', [$id]);
    }

    public static function create(array $d, int $userId): int
    {
        static::exec(
            'INSERT INTO incidencias (plataforma_id, tipo_id, titulo, descripcion, creado_por) VALUES (?, ?, ?, ?, ?)',
            [$d['plataforma_id'], $d['tipo_id'], $d['titulo'], $d['descripcion'], $userId]
        );
        return static::lastId();
    }

    public static function update(int $id, array $d): void
    {
        static::exec(
            'UPDATE incidencias SET plataforma_id = ?, tipo_id = ?, titulo = ?, descripcion = ? WHERE id = ?',
            [$d['plataforma_id'], $d['tipo_id'], $d['titulo'], $d['descripcion'], $id]
        );
    }

    public static function setActivo(int $id, bool $activo): void
    {
        static::exec('UPDATE incidencias SET activo = ? WHERE id = ?', [(int) $activo, $id]);
    }
}
