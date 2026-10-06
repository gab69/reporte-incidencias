<?php
namespace App\Services;

use App\Core\Auth;
use App\Core\Database;
use App\Core\ValidationException;
use App\Models\Auditoria;
use App\Models\Incidencia;
use App\Models\Paso;
use App\Models\Plataforma;
use App\Models\Tipo;

/** Reglas de negocio para crear/editar incidencias y cambiar su estado. */
final class IncidenciaService
{
    public function __construct(private ImageService $images)
    {
    }

    /** @return array{0:int,1:bool} [id, esNueva] */
    public function guardar(int $id, array $post, array $files): array
    {
        $old = null;
        $oldPasos = [];
        if ($id) {
            $old = Incidencia::find($id);
            if (!$old || !Auth::canEdit($old)) {
                throw new ValidationException('Solo quien creó la incidencia (o un administrador) puede editarla.');
            }
            $oldPasos = Paso::porIncidencia($id);
        }
        $oldImages = array_filter(array_column($oldPasos, 'imagen'));

        $data = [
            'titulo'        => trim($post['titulo'] ?? ''),
            'descripcion'   => trim($post['descripcion'] ?? ''),
            'plataforma_id' => (int) ($post['plataforma_id'] ?? 0),
            'tipo_id'       => (int) ($post['tipo_id'] ?? 0),
        ];
        $platName = Plataforma::nombre($data['plataforma_id']);
        $tipoName = Tipo::nombre($data['tipo_id']);

        $uploaded = [];
        try {
            $pasos = $this->procesarPasos($post['pasos'] ?? [], $files['imagen'] ?? null, $oldImages, $uploaded);
            if ($data['titulo'] === '') {
                throw new ValidationException('Escribe un título para la incidencia.');
            }
            if (!$platName || !$tipoName) {
                throw new ValidationException('Elige una plataforma y un tipo válidos.');
            }
            if (!$pasos) {
                throw new ValidationException('Agrega al menos un paso con texto o imagen.');
            }
            $pdo = Database::pdo();
            $pdo->beginTransaction();
            try {
                if ($old) {
                    Incidencia::update($id, $data);
                } else {
                    $id = Incidencia::create($data, Auth::id());
                }
                Paso::reemplazar($id, $pasos);
                $pdo->commit();
            } catch (\Throwable $e) {
                $pdo->rollBack();
                throw $e;
            }
        } catch (\Throwable $e) {
            foreach ($uploaded as $name) {
                $this->images->delete($name);
            }
            throw $e;
        }

        foreach ($oldImages as $name) { // imágenes que ya no se usan
            if (!in_array($name, array_column($pasos, 'imagen'), true)) {
                $this->images->delete($name);
            }
        }
        $this->auditar($old, $oldPasos, $id, $data, $platName, $tipoName, $pasos);
        return [$id, $old === null];
    }

    /** Activa/desactiva y devuelve el nuevo estado. */
    public function cambiarEstado(int $id): bool
    {
        $inc = Incidencia::find($id);
        if (!$inc || !Auth::canEdit($inc)) {
            throw new ValidationException('Solo quien creó la incidencia (o un administrador) puede cambiar su estado.');
        }
        $nuevo = !$inc['activo'];
        Incidencia::setActivo($id, $nuevo);
        Auditoria::registrar($nuevo ? 'Reactivó incidencia' : 'Desactivó incidencia', 'incidencia', $id, $inc['titulo']);
        return $nuevo;
    }

    /** Convierte los pasos enviados en [texto, imagen], subiendo las imágenes nuevas como WebP. */
    private function procesarPasos(array $posted, ?array $files, array $oldImages, array &$uploaded): array
    {
        $pasos = [];
        foreach ($posted as $k => $p) {
            $img = (ImageService::isValidName($p['img'] ?? null) && in_array($p['img'], $oldImages, true)) ? $p['img'] : null;
            $error = $files['error'][$k] ?? UPLOAD_ERR_NO_FILE;
            if ($error === UPLOAD_ERR_OK) {
                $name = $this->images->toWebp($files['tmp_name'][$k]);
                if (!$name) {
                    throw new ValidationException('Una de las imágenes no es válida. Usa JPG, PNG, GIF o WebP.');
                }
                $uploaded[] = $name;
                $img = $name;
            } elseif (in_array($error, [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true)) {
                throw new ValidationException('Una imagen supera el tamaño máximo permitido por el servidor.');
            }
            $texto = trim($p['texto'] ?? '');
            if ($texto !== '' || $img) {
                $pasos[] = ['texto' => $texto, 'imagen' => $img];
            }
        }
        return $pasos;
    }

    private function auditar(?array $old, array $oldPasos, int $id, array $data, string $plat, string $tipo, array $pasos): void
    {
        if (!$old) {
            Auditoria::registrar('Creó incidencia', 'incidencia', $id, "[$plat · $tipo] {$data['titulo']}");
            return;
        }
        $cambios = [];
        if ($old['titulo'] !== $data['titulo']) {
            $cambios[] = 'Título: «' . cut($old['titulo']) . '» → «' . cut($data['titulo']) . '»';
        }
        if ($old['plat'] !== $plat) {
            $cambios[] = "Plataforma: {$old['plat']} → $plat";
        }
        if ($old['tipo'] !== $tipo) {
            $cambios[] = "Tipo: {$old['tipo']} → $tipo";
        }
        if (trim((string) $old['descripcion']) !== $data['descripcion']) {
            $cambios[] = 'Descripción modificada';
        }
        $antes = array_map(fn($r) => [$r['texto'], $r['imagen']], $oldPasos);
        $despues = array_map(fn($r) => [$r['texto'], $r['imagen']], $pasos);
        if ($antes !== $despues) {
            $cambios[] = 'Pasos modificados (' . count($antes) . ' → ' . count($despues) . ')';
        }
        if ($cambios) {
            Auditoria::registrar('Editó incidencia', 'incidencia', $id, cut($data['titulo'], 50) . ' — ' . implode('; ', $cambios));
        }
    }
}
