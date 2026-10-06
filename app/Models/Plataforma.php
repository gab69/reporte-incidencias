<?php
namespace App\Models;

final class Plataforma extends Catalogo
{
    protected static function table(): string
    {
        return 'plataformas';
    }

    protected static function columns(): array
    {
        return ['nombre', 'color'];
    }

    /** Plataformas activas con la cantidad de incidencias activas (para los filtros). */
    public static function conConteo(): array
    {
        return static::rows(
            'SELECT p.*, COUNT(i.id) n FROM plataformas p
             LEFT JOIN incidencias i ON i.plataforma_id = p.id AND i.activo = 1
             WHERE p.activo = 1 GROUP BY p.id ORDER BY p.nombre'
        );
    }
}
