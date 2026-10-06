<?php
/**
 * Funciones auxiliares globales usadas por las vistas.
 */

use App\Core\Config;
use App\Core\Csrf;
use App\Models\Ajuste;

function e(mixed $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

/** JSON seguro para incrustar dentro de <script> o atributos. */
function js(mixed $v): string
{
    return json_encode($v, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);
}

function url(string $route = 'incidencias/index', array $query = []): string
{
    $query = array_filter($query, fn($v) => $v !== null && $v !== '' && $v !== 0 && $v !== false);
    return 'index.php?r=' . $route . ($query ? '&' . http_build_query($query) : '');
}

function asset(string $path): string
{
    $file = BASE_PATH . '/assets/' . $path;
    return 'assets/' . $path . '?v=' . (is_file($file) ? filemtime($file) : 1);
}

function upload_url(string $name): string
{
    return Config::get('uploads.url') . $name;
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(Csrf::token()) . '">';
}

function cut(string $s, int $n = 40): string
{
    return mb_strlen($s) > $n ? mb_substr($s, 0, $n) . '…' : $s;
}

function fecha(?string $d, string $format = 'd/m/Y'): string
{
    return $d ? date($format, strtotime($d)) : '';
}

/** Resalta en <mark> las coincidencias del texto buscado. */
function hl(string $text, string $q): string
{
    $html = e($text);
    if ($q === '') {
        return $html;
    }
    return preg_replace('/(' . preg_quote(e($q), '/') . ')/iu', '<mark>$1</mark>', $html) ?? $html;
}

/** Mezcla dos colores #rrggbb ($ratio = proporción de $with). */
function mix(string $hex, string $with, float $ratio): string
{
    $a = sscanf($hex, '#%02x%02x%02x');
    $b = sscanf($with, '#%02x%02x%02x');
    $out = '#';
    for ($i = 0; $i < 3; $i++) {
        $out .= sprintf('%02x', (int) round($a[$i] * (1 - $ratio) + $b[$i] * $ratio));
    }
    return $out;
}

/** Valor de configuración visual; si la base no responde, usa el valor por defecto. */
function ajuste(string $key): string
{
    try {
        return Ajuste::get($key);
    } catch (\Throwable) {
        return Ajuste::DEFAULTS[$key] ?? '';
    }
}

function ico(string $name): string
{
    static $paths = [
        'list'   => '<path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>',
        'plus'   => '<path d="M12 5v14M5 12h14"/>',
        'grid'   => '<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>',
        'users'  => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>',
        'clock'  => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
        'out'    => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/>',
        'in'     => '<path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M15 12H3"/>',
        'search' => '<circle cx="11" cy="11" r="8"/><path d="M21 21l-4.3-4.3"/>',
        'cog'    => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>',
        'shield' => '<path d="M12 2l8 4v6c0 5-3.5 8.5-8 10-4.5-1.5-8-5-8-10V6z"/><path d="M9 12l2 2 4-4"/>',
    ];
    return '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' . $paths[$name] . '</svg>';
}
