<?php
namespace App\Core;

/** Usuario en sesión y reglas de permisos. */
final class Auth
{
    public static function user(): ?array
    {
        return Session::get('user');
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function id(): int
    {
        return (int) (self::user()['id'] ?? 0);
    }

    public static function isAdmin(): bool
    {
        return (self::user()['rol'] ?? '') === 'admin';
    }

    public static function login(array $usuario): void
    {
        Session::regenerate();
        Session::set('user', ['id' => (int) $usuario['id'], 'nombre' => $usuario['nombre'], 'rol' => $usuario['rol']]);
    }

    public static function logout(): void
    {
        Session::forget('user');
        Session::regenerate();
    }

    public static function renameInSession(string $nombre): void
    {
        $user = self::user();
        if ($user) {
            $user['nombre'] = $nombre;
            Session::set('user', $user);
        }
    }

    /** Solo el creador de la incidencia o un administrador pueden modificarla. */
    public static function canEdit(array $incidencia): bool
    {
        return self::check() && (self::isAdmin() || (int) $incidencia['creado_por'] === self::id());
    }
}
