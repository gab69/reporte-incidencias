<?php
namespace App\Models;

use App\Core\Auth;
use App\Core\Model;

/** Bitácora de modificaciones hechas por los usuarios. */
final class Auditoria extends Model
{
    public static function registrar(string $accion, string $entidad, ?int $entidadId, string $detalle = ''): void
    {
        static::exec(
            'INSERT INTO auditoria (usuario_id, usuario_nombre, accion, entidad, entidad_id, detalle) VALUES (?, ?, ?, ?, ?, ?)',
            [Auth::id() ?: null, Auth::user()['nombre'] ?? '—', $accion, $entidad, $entidadId, mb_substr($detalle, 0, 500)]
        );
    }

    /** Página de resultados. Filtros: q (texto), u (usuario), e (entidad). */
    public static function pagina(array $f, int $page, int $perPage): array
    {
        $where = ['1 = 1'];
        $params = [];
        if ($f['q'] !== '') {
            $where[] = '(usuario_nombre LIKE ? OR accion LIKE ? OR detalle LIKE ?)';
            $like = '%' . $f['q'] . '%';
            array_push($params, $like, $like, $like);
        }
        if ($f['u'] !== '') {
            $where[] = 'usuario_nombre = ?';
            $params[] = $f['u'];
        }
        if ($f['e'] !== '') {
            $where[] = 'entidad = ?';
            $params[] = $f['e'];
        }
        $cond = implode(' AND ', $where);
        $total = (int) static::scalar("SELECT COUNT(*) FROM auditoria WHERE $cond", $params);
        $pages = max(1, (int) ceil($total / $perPage));
        $page = min(max(1, $page), $pages);
        $rows = static::rows(
            "SELECT * FROM auditoria WHERE $cond ORDER BY fecha DESC, id DESC LIMIT " . (int) $perPage . ' OFFSET ' . (int) (($page - 1) * $perPage),
            $params
        );
        return compact('rows', 'total', 'page', 'pages');
    }

    public static function usuarios(): array
    {
        return static::rows('SELECT usuario_nombre, COUNT(*) n FROM auditoria GROUP BY usuario_nombre ORDER BY usuario_nombre');
    }
}
