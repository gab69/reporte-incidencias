<?php
namespace App\Core;

use App\Services\SchemaInstaller;

/** Arranque: errores, sesión, esquema de base de datos y despacho de la ruta. */
final class App
{
    public function __construct(private array $routes)
    {
    }

    public function run(): void
    {
        error_reporting(E_ALL);
        ini_set('display_errors', '0');
        ini_set('log_errors', '1');
        ob_start();
        register_shutdown_function([$this, 'onShutdown']);
        Session::start();

        try {
            if (Session::get('schema') !== 'v4:' . Config::get('db.name')) {
                SchemaInstaller::ensure();
                Session::set('schema', 'v4:' . Config::get('db.name'));
            }
            $output = (new Router($this->routes))->dispatch();
        } catch (\Throwable $e) {
            http_response_code(500);
            $detail = Config::get('debug') ? $e->getMessage() : 'Revisa los datos de conexión en config/config.php y que MySQL esté encendido.';
            $output = View::render('errors/error', ['title' => 'Error', 'heading' => 'No se pudo completar la solicitud', 'message' => $detail]);
        }

        // Texto suelto emitido por PHP (avisos): se descarta y se informa en un modal.
        $stray = trim(str_replace("\xEF\xBB\xBF", '', strip_tags((string) ob_get_clean())));
        if ($stray !== '' && is_string($output) && str_contains($output, '</body>')) {
            $note = '<script>UI.notify("warn",' . js('El servidor devolvió este mensaje: ' . mb_substr($stray, 0, 300)) . ',6000)</script></body>';
            $output = str_replace('</body>', $note, $output);
        }
        echo $output;
    }

    public function onShutdown(): void
    {
        $e = error_get_last();
        if ($e && in_array($e['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR], true)) {
            echo '<div style="position:fixed;left:16px;right:16px;bottom:16px;z-index:999;background:#fdeceb;color:#b3281d;border:1px solid #f5c2bd;padding:14px 16px;border-radius:10px;font:14px/1.5 system-ui"><b>Error del servidor:</b> '
                . htmlspecialchars($e['message'] . ' (línea ' . $e['line'] . ')') . '</div>';
        }
    }
}
