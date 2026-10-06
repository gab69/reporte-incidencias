<?php
namespace App\Models;

final class Tipo extends Catalogo
{
    protected static function table(): string
    {
        return 'tipos';
    }
}
