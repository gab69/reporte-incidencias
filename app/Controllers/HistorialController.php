<?php
namespace App\Controllers;

use App\Core\Config;
use App\Core\Controller;
use App\Models\Auditoria;

final class HistorialController extends Controller
{
    public function index(): string
    {
        $filtros = [
            'q' => trim($_GET['q'] ?? ''),
            'u' => trim($_GET['u'] ?? ''),
            'e' => trim($_GET['e'] ?? ''),
        ];
        $pagina = Auditoria::pagina($filtros, (int) ($_GET['page'] ?? 1), (int) Config::get('paginacion.historial', 20));
        if ($this->isAjax()) {
            return $this->fragment('historial/_tabla', $pagina);
        }
        return $this->view('historial/index', $pagina + [
            'title'    => 'Historial',
            'scripts'  => ['live.js'],
            'f'        => $filtros,
            'usuarios' => Auditoria::usuarios(),
        ]);
    }
}
