<?php
namespace App\Core;

/** Base de los controladores. */
abstract class Controller
{
    protected function view(string $view, array $data = []): string
    {
        return View::render($view, $data);
    }

    protected function fragment(string $view, array $data = []): string
    {
        return View::fragment($view, $data);
    }

    protected function redirect(string $route, array $query = []): void
    {
        header('Location: ' . url($route, $query));
        exit;
    }

    protected function flash(string $message, string $type = 'ok'): void
    {
        Session::flash($message, $type);
    }

    protected function isAjax(): bool
    {
        return isset($_GET['ajax']);
    }
}
