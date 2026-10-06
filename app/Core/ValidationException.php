<?php
namespace App\Core;

/** Error de negocio/validación: el router lo convierte en un mensaje para el usuario. */
final class ValidationException extends \RuntimeException
{
    public function __construct(string $message, public readonly string $type = 'err')
    {
        parent::__construct($message);
    }
}
