<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Ajuste;
use App\Services\AjusteService;
use App\Services\ImageService;

final class AjusteController extends Controller
{
    public function index(): string
    {
        return $this->view('ajustes/index', [
            'title'    => 'Configuración',
            'scripts'  => ['ajustes.js'],
            'ajustes'  => Ajuste::all(),
            'defaults' => Ajuste::DEFAULTS,
        ]);
    }

    public function guardar(): void
    {
        (new AjusteService(ImageService::fromConfig()))->guardar($_POST, $_FILES);
        $this->flash('La configuración se guardó correctamente.');
        $this->redirect('ajustes/index');
    }
}
