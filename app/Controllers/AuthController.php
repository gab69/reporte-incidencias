<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Usuario;

final class AuthController extends Controller
{
    public function login(): string
    {
        if (Auth::check()) {
            $this->redirect('incidencias/index');
        }
        return $this->view('auth/login', ['title' => 'Ingresar', 'bare' => true]);
    }

    public function entrar(): void
    {
        $usuario = Usuario::findByLogin(trim($_POST['usuario'] ?? ''));
        if ($usuario && password_verify($_POST['clave'] ?? '', $usuario['clave_hash'])) {
            if (!$usuario['activo']) {
                $this->flash('Tu usuario está desactivado. Contacta al administrador.', 'warn');
                $this->redirect('auth/login');
            }
            Auth::login($usuario);
            $this->flash('Bienvenido(a), ' . $usuario['nombre'] . '.');
            $this->redirect('incidencias/index');
        }
        $this->flash('Usuario o contraseña incorrectos.', 'err');
        $this->redirect('auth/login');
    }

    public function salir(): void
    {
        Auth::logout();
        $this->flash('Cerraste sesión correctamente.', 'info');
        $this->redirect('incidencias/index');
    }
}
