<?php
namespace App\Core;

final class Csrf
{
    public static function token(): string
    {
        $token = Session::get('csrf');
        if (!$token) {
            $token = bin2hex(random_bytes(16));
            Session::set('csrf', $token);
        }
        return $token;
    }

    public static function isValid(?string $token): bool
    {
        return is_string($token) && hash_equals((string) Session::get('csrf', ''), $token);
    }
}
