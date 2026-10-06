<?php
namespace App\Services;

use App\Core\Config;
use App\Core\ValidationException;
use App\Models\Ajuste;
use App\Models\Auditoria;

/** Personalización de la plataforma: nombre, colores, logo y favicon. */
final class AjusteService
{
    private const LABELS = ['app_name' => 'nombre', 'color_primario' => 'color principal', 'color_menu' => 'color del menú', 'logo' => 'logo', 'favicon' => 'favicon'];

    public function __construct(private ImageService $images)
    {
    }

    public function guardar(array $post, array $files): void
    {
        $nombre = trim($post['app_name'] ?? '');
        if ($nombre === '' || mb_strlen($nombre) > 60) {
            throw new ValidationException('El nombre de la plataforma es obligatorio (máximo 60 caracteres).');
        }
        foreach (['color_primario', 'color_menu'] as $k) {
            if (!preg_match('/^#[0-9a-fA-F]{6}$/', $post[$k] ?? '')) {
                throw new ValidationException('Los colores deben tener el formato #RRGGBB.');
            }
        }
        $nuevos = [
            'app_name'       => $nombre,
            'color_primario' => strtolower($post['color_primario']),
            'color_menu'     => strtolower($post['color_menu']),
            'logo'           => $this->imagen('logo', $post, $files, fn($tmp) => $this->images->toWebp($tmp, (int) Config::get('uploads.logo_max_px'))),
            'favicon'        => $this->imagen('favicon', $post, $files, fn($tmp) => $this->images->toFavicon($tmp)),
        ];
        $cambios = [];
        foreach ($nuevos as $k => $v) {
            if (Ajuste::get($k) !== $v) {
                if (in_array($k, ['logo', 'favicon'], true)) {
                    $this->images->delete(Ajuste::get($k));
                }
                Ajuste::set($k, $v);
                $cambios[] = self::LABELS[$k];
            }
        }
        if ($cambios) {
            Auditoria::registrar('Editó configuración', 'ajuste', null, 'Cambió: ' . implode(', ', $cambios));
        }
    }

    /** Devuelve el nombre de archivo resultante: el nuevo si se subió, '' si se quitó, o el actual. */
    private function imagen(string $campo, array $post, array $files, callable $convertir): string
    {
        $actual = Ajuste::get($campo);
        if (!empty($post['quitar_' . $campo])) {
            return '';
        }
        $file = $files[$campo] ?? null;
        $error = $file['error'] ?? UPLOAD_ERR_NO_FILE;
        if ($error === UPLOAD_ERR_NO_FILE) {
            return $actual;
        }
        if ($error !== UPLOAD_ERR_OK) {
            throw new ValidationException('No se pudo subir el archivo de ' . self::LABELS[$campo] . '. Revisa su tamaño.');
        }
        $name = $convertir($file['tmp_name']);
        if (!$name) {
            throw new ValidationException('El archivo de ' . self::LABELS[$campo] . ' no es una imagen válida.');
        }
        return $name;
    }
}
