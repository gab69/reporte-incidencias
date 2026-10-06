<?php
namespace App\Services;

use App\Core\Config;

/** Procesa imágenes subidas: conversión a WebP, favicon y borrado seguro. */
final class ImageService
{
    private const NAME_PATTERN = '/^[a-f0-9]{16}\.(webp|png|ico)$/';

    public function __construct(private string $dir, private int $maxPx, private int $quality)
    {
    }

    public static function fromConfig(): self
    {
        return new self(Config::get('uploads.path'), (int) Config::get('uploads.max_px'), (int) Config::get('uploads.quality'));
    }

    /** Convierte JPG/PNG/GIF/WebP a WebP reducido. Devuelve el nombre del archivo o null si no es válida. */
    public function toWebp(string $tmp, ?int $maxPx = null): ?string
    {
        $img = $this->load($tmp);
        if (!$img) {
            return null;
        }
        $max = $maxPx ?? $this->maxPx;
        $w = imagesx($img);
        $h = imagesy($img);
        if (max($w, $h) > $max) {
            $img = $w >= $h ? imagescale($img, $max, -1) : imagescale($img, (int) ($w * $max / $h), $max);
            if (!$img) {
                return null;
            }
        }
        $name = $this->randomName('webp');
        return imagewebp($img, $this->dir . $name, $this->quality) ? $name : null;
    }

    /** Favicon: acepta .ico o cualquier imagen (se guarda como PNG 64x64). */
    public function toFavicon(string $tmp): ?string
    {
        $head = (string) @file_get_contents($tmp, false, null, 0, 4);
        if ($head === "\x00\x00\x01\x00") {
            $name = $this->randomName('ico');
            return @copy($tmp, $this->dir . $name) ? $name : null;
        }
        $img = $this->load($tmp);
        if (!$img) {
            return null;
        }
        $size = 64;
        $canvas = imagecreatetruecolor($size, $size);
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        imagefill($canvas, 0, 0, imagecolorallocatealpha($canvas, 0, 0, 0, 127));
        $w = imagesx($img);
        $h = imagesy($img);
        $ratio = min($size / $w, $size / $h);
        $nw = max(1, (int) round($w * $ratio));
        $nh = max(1, (int) round($h * $ratio));
        imagecopyresampled($canvas, $img, (int) (($size - $nw) / 2), (int) (($size - $nh) / 2), 0, 0, $nw, $nh, $w, $h);
        $name = $this->randomName('png');
        return imagepng($canvas, $this->dir . $name) ? $name : null;
    }

    public function delete(?string $name): void
    {
        if ($name && preg_match(self::NAME_PATTERN, $name)) {
            @unlink($this->dir . $name);
        }
    }

    public static function isValidName(?string $name): bool
    {
        return is_string($name) && (bool) preg_match(self::NAME_PATTERN, $name);
    }

    private function load(string $tmp)
    {
        $info = @getimagesize($tmp);
        if (!$info) {
            return null;
        }
        $img = match ($info[2]) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($tmp),
            IMAGETYPE_PNG  => @imagecreatefrompng($tmp),
            IMAGETYPE_GIF  => @imagecreatefromgif($tmp),
            IMAGETYPE_WEBP => @imagecreatefromwebp($tmp),
            default        => false,
        };
        if (!$img) {
            return null;
        }
        if ($info[2] === IMAGETYPE_JPEG && function_exists('exif_read_data')) { // corrige la rotación de fotos de celular
            $exif = @exif_read_data($tmp);
            $angle = [3 => 180, 6 => -90, 8 => 90][$exif['Orientation'] ?? 0] ?? 0;
            if ($angle) {
                $img = imagerotate($img, $angle, 0);
            }
        }
        imagepalettetotruecolor($img);
        imagealphablending($img, false);
        imagesavealpha($img, true);
        return $img;
    }

    private function randomName(string $ext): string
    {
        return bin2hex(random_bytes(8)) . '.' . $ext;
    }
}
