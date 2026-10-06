<?php
namespace App\Core;

/** Renderiza plantillas PHP de app/Views dentro del layout. */
final class View
{
    public static function render(string $view, array $data = [], string $layout = 'layout'): string
    {
        $data['content'] = self::fragment($view, $data);
        return self::fragment($layout, $data);
    }

    public static function fragment(string $view, array $data = []): string
    {
        return self::capture(BASE_PATH . '/app/Views/' . $view . '.php', $data);
    }

    private static function capture(string $__file, array $__data): string
    {
        extract($__data, EXTR_SKIP);
        ob_start();
        include $__file;
        return (string) ob_get_clean();
    }
}
