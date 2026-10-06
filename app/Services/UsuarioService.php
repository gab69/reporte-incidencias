<?php
namespace App\Services;

use App\Core\Auth;
use App\Core\ValidationException;
use App\Models\Auditoria;
use App\Models\Usuario;

/** Alta y edición de usuarios (no se eliminan: se desactivan). */
final class UsuarioService
{
    /** @return array{0:string,1:string} [mensaje, tipo] */
    public function guardar(array $post): array
    {
        $id = (int) ($post['id'] ?? 0);
        $login = trim($post['usuario'] ?? '');
        $nombre = trim($post['nombre'] ?? '') ?: $login;
        $clave = (string) ($post['clave'] ?? '');
        $rol = ($post['rol'] ?? '') === 'admin' ? 'admin' : 'tecnico';
        $activo = $id ? isset($post['activo']) : true;

        if (!preg_match('/^[A-Za-z0-9._-]{3,50}$/', $login)) {
            throw new ValidationException('El usuario debe tener de 3 a 50 caracteres (letras, números, punto o guion).');
        }
        if (($id === 0 && strlen($clave) < 6) || ($clave !== '' && strlen($clave) < 6)) {
            throw new ValidationException('La contraseña debe tener al menos 6 caracteres.');
        }
        if ($id && $id === Auth::id() && ($rol !== 'admin' || !$activo)) {
            throw new ValidationException('No puedes quitarte el rol de administrador ni desactivar tu propio usuario.', 'warn');
        }

        try {
            return $id ? $this->actualizar($id, $login, $nombre, $clave, $rol, $activo) : $this->crear($login, $nombre, $clave, $rol);
        } catch (\PDOException) {
            throw new ValidationException('Ese nombre de usuario ya está en uso.');
        }
    }

    private function crear(string $login, string $nombre, string $clave, string $rol): array
    {
        $id = Usuario::create(['usuario' => $login, 'nombre' => $nombre, 'clave_hash' => password_hash($clave, PASSWORD_DEFAULT), 'rol' => $rol]);
        Auditoria::registrar('Creó usuario', 'usuario', $id, "$login ($rol)");
        return ['Usuario creado correctamente.', 'ok'];
    }

    private function actualizar(int $id, string $login, string $nombre, string $clave, string $rol, bool $activo): array
    {
        $old = Usuario::find($id);
        if (!$old) {
            throw new ValidationException('Usuario no encontrado.');
        }
        Usuario::update($id, ['usuario' => $login, 'nombre' => $nombre, 'rol' => $rol, 'activo' => $activo]);
        if ($clave !== '') {
            Usuario::setPassword($id, password_hash($clave, PASSWORD_DEFAULT));
        }
        if ($id === Auth::id()) {
            Auth::renameInSession($nombre);
        }
        $cambios = [];
        if ($old['usuario'] !== $login) {
            $cambios[] = "usuario: {$old['usuario']} → $login";
        }
        if ($old['nombre'] !== $nombre) {
            $cambios[] = 'nombre';
        }
        if ($old['rol'] !== $rol) {
            $cambios[] = "rol: {$old['rol']} → $rol";
        }
        if ((bool) $old['activo'] !== $activo) {
            $cambios[] = $activo ? 'reactivado' : 'desactivado';
        }
        if ($clave !== '') {
            $cambios[] = 'clave cambiada';
        }
        Auditoria::registrar('Editó usuario', 'usuario', $id, $login . ($cambios ? ' — ' . implode('; ', $cambios) : ''));
        return ['Usuario actualizado correctamente.', $activo ? 'ok' : 'warn'];
    }
}
