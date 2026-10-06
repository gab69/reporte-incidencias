<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\ValidationException;
use App\Models\Auditoria;
use App\Models\Plataforma;
use App\Models\Tipo;

/** Plataformas y tipos de incidencia: crear, renombrar, recolorear y activar/desactivar. */
final class CatalogoController extends Controller
{
    public function index(): string
    {
        return $this->view('catalogos/index', [
            'title'       => 'Catálogos',
            'plataformas' => Plataforma::all(),
            'tipos'       => Tipo::all(),
        ]);
    }

    public function guardar(): void
    {
        $esTipo = ($_POST['k'] ?? '') === 'tipo';
        $modelo = $esTipo ? Tipo::class : Plataforma::class;
        $etiqueta = $esTipo ? 'tipo' : 'plataforma';
        $id = (int) ($_POST['id'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');
        if ($nombre === '') {
            throw new ValidationException('El nombre no puede estar vacío.');
        }
        $datos = [
            'nombre' => $nombre,
            'color'  => preg_match('/^#[0-9a-fA-F]{6}$/', $_POST['color'] ?? '') ? $_POST['color'] : '#475569',
            'activo' => $id ? isset($_POST['activo']) : true,
        ];
        try {
            if ($id) {
                $modelo::actualizar($id, $datos);
            } else {
                $id = $modelo::crear($datos);
            }
        } catch (\PDOException) {
            throw new ValidationException('Ya existe un elemento con ese nombre.');
        }
        $editado = !empty($_POST['id']);
        Auditoria::registrar(($editado ? 'Editó ' : 'Creó ') . $etiqueta, $etiqueta, $id, $nombre . ($editado && !$datos['activo'] ? ' (desactivado)' : ''));
        $this->flash(
            ($esTipo ? 'Tipo' : 'Plataforma') . ($editado ? ' actualizado correctamente.' : ' agregado correctamente.'),
            $editado && !$datos['activo'] ? 'warn' : 'ok'
        );
        $this->redirect('catalogos/index');
    }
}
