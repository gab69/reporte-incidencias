<?php
namespace App\Models;

use App\Core\Model;

final class Paso extends Model
{
    public static function porIncidencia(int $incidenciaId): array
    {
        return static::rows('SELECT texto, imagen FROM pasos WHERE incidencia_id = ? ORDER BY orden', [$incidenciaId]);
    }

    /** Sustituye todos los pasos de la incidencia (se llama dentro de una transacción). */
    public static function reemplazar(int $incidenciaId, array $pasos): void
    {
        static::exec('DELETE FROM pasos WHERE incidencia_id = ?', [$incidenciaId]);
        $orden = 1;
        foreach ($pasos as $p) {
            static::exec('INSERT INTO pasos (incidencia_id, orden, texto, imagen) VALUES (?, ?, ?, ?)', [$incidenciaId, $orden++, $p['texto'], $p['imagen']]);
        }
    }
}
