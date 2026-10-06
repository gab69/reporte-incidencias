<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Usuario;
use App\Services\UsuarioService;

final class UsuarioController extends Controller
{
    public function index(): string
    {
        return $this->view('usuarios/index', [
            'title'    => 'Usuarios',
            'scripts'  => ['usuarios.js'],
            'usuarios' => Usuario::all(),
        ]);
    }

    public function guardar(): void
    {
        [$mensaje, $tipo] = (new UsuarioService())->guardar($_POST);
        $this->flash($mensaje, $tipo);
        $this->redirect('usuarios/index');
    }
}
