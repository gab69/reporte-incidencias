<?php
namespace App\Core;

/** Resuelve ?r=controlador/accion, valida método, permisos y CSRF, y ejecuta el controlador. */
final class Router
{
    public function __construct(private array $routes)
    {
    }

    public function dispatch(): ?string
    {
        $route = $_GET['r'] ?? 'incidencias/index';
        $definition = $this->routes[$route] ?? null;
        if (!$definition) {
            http_response_code(404);
            return View::render('errors/error', ['title' => 'No encontrado', 'heading' => 'Página no encontrada', 'message' => 'La dirección que buscas no existe.']);
        }

        [$method, $class, $action, $access] = $definition;
        if ($_SERVER['REQUEST_METHOD'] !== $method) {
            header('Location: ' . url());
            exit;
        }
        $this->authorize($access);
        if ($method === 'POST') {
            $this->guardPost();
        }

        try {
            $fqcn = 'App\\Controllers\\' . $class;
            return (new $fqcn())->$action();
        } catch (ValidationException $e) {
            Session::flash($e->getMessage(), $e->type);
            header('Location: ' . $this->backUrl());
            exit;
        }
    }

    private function authorize(string $access): void
    {
        if ($access === 'public') {
            return;
        }
        if (!Auth::check()) {
            Session::flash('Inicia sesión para continuar.', 'warn');
            header('Location: ' . url('auth/login'));
            exit;
        }
        if ($access === 'admin' && !Auth::isAdmin()) {
            Session::flash('Esta sección es solo para administradores.', 'err');
            header('Location: ' . url());
            exit;
        }
    }

    private function guardPost(): void
    {
        if (empty($_POST) && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
            Session::flash('Los archivos enviados superan el tamaño máximo permitido por el servidor. Sube imágenes más livianas.', 'err');
            header('Location: ' . $this->backUrl());
            exit;
        }
        if (!Csrf::isValid($_POST['csrf'] ?? null)) {
            Session::flash('Tu sesión expiró. Vuelve a intentarlo.', 'warn');
            header('Location: ' . url());
            exit;
        }
    }

    private function backUrl(): string
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        $host = parse_url($referer, PHP_URL_HOST);
        $mine = parse_url('http://' . ($_SERVER['HTTP_HOST'] ?? ''), PHP_URL_HOST);
        return ($referer !== '' && $host === $mine) ? $referer : url();
    }
}
