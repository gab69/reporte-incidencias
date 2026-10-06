<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Incidencia;
use App\Models\Paso;
use App\Models\Plataforma;
use App\Models\Tipo;
use App\Services\ImageService;
use App\Services\IncidenciaService;

final class IncidenciaController extends Controller
{
    private IncidenciaService $service;

    public function __construct()
    {
        $this->service = new IncidenciaService(ImageService::fromConfig());
    }

    /** Listado con búsqueda en vivo (si viene ?ajax=1 devuelve solo las tarjetas). */
    public function index(): string
    {
        $filtros = $this->filtros();
        $rows = Incidencia::buscar($filtros, Auth::user());
        if ($this->isAjax()) {
            return $this->fragment('incidencias/_items', ['rows' => $rows, 'q' => $filtros['q']]);
        }
        return $this->view('incidencias/lista', [
            'title'       => 'Incidencias',
            'scripts'     => ['live.js'],
            'f'           => $filtros,
            'rows'        => $rows,
            'plataformas' => Plataforma::conConteo(),
            'tipos'       => Tipo::activos(),
        ]);
    }

    public function ver(): string
    {
        $inc = Incidencia::find((int) ($_GET['id'] ?? 0));
        if (!$inc || (!$inc['activo'] && !Auth::canEdit($inc))) {
            $this->flash('La incidencia no existe o ya no está disponible.', 'warn');
            $this->redirect('incidencias/index');
        }
        return $this->view('incidencias/ver', [
            'title'    => $inc['titulo'],
            'inc'      => $inc,
            'pasos'    => Paso::porIncidencia((int) $inc['id']),
            'canEdit'  => Auth::canEdit($inc),
        ]);
    }

    public function form(): string
    {
        $id = (int) ($_GET['id'] ?? 0);
        $inc = ['id' => 0, 'plataforma_id' => 0, 'tipo_id' => 0, 'titulo' => '', 'descripcion' => '', 'creado_por' => Auth::id()];
        $pasos = [['texto' => '', 'imagen' => '']];
        if ($id) {
            $inc = Incidencia::find($id);
            if (!$inc) {
                $this->redirect('incidencias/index');
            }
            if (!Auth::canEdit($inc)) {
                $this->flash('Solo quien creó la incidencia o un administrador puede editarla.', 'err');
                $this->redirect('incidencias/ver', ['id' => $id]);
            }
            $pasos = Paso::porIncidencia($id) ?: $pasos;
        }
        return $this->view('incidencias/form', [
            'title'       => $id ? 'Editar incidencia' : 'Nueva incidencia',
            'scripts'     => ['form-incidencia.js'],
            'inc'         => $inc,
            'pasos'       => $pasos,
            'plataformas' => Plataforma::activosOActual((int) $inc['plataforma_id']),
            'tipos'       => Tipo::activosOActual((int) $inc['tipo_id']),
        ]);
    }

    public function guardar(): void
    {
        [$id, $esNueva] = $this->service->guardar((int) ($_POST['id'] ?? 0), $_POST, $_FILES);
        $this->flash($esNueva ? 'La incidencia se registró correctamente.' : 'Los cambios se guardaron correctamente.');
        $this->redirect('incidencias/ver', ['id' => $id]);
    }

    public function toggle(): void
    {
        $id = (int) ($_POST['id'] ?? 0);
        $activa = $this->service->cambiarEstado($id);
        $this->flash(
            $activa ? 'Incidencia reactivada: vuelve a ser visible para todos.' : 'Incidencia desactivada: ya no se muestra en el listado público.',
            $activa ? 'ok' : 'warn'
        );
        $this->redirect('incidencias/ver', ['id' => $id]);
    }

    private function filtros(): array
    {
        $logueado = Auth::check();
        return [
            'q'     => trim($_GET['q'] ?? ''),
            'p'     => (int) ($_GET['p'] ?? 0),
            't'     => (int) ($_GET['t'] ?? 0),
            'mias'  => $logueado && !empty($_GET['mias']),
            'inact' => $logueado && !empty($_GET['inact']),
        ];
    }
}
