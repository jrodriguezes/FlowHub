<?php

namespace App\Exceptions;

use RuntimeException;

class UnsupportedActionException extends RuntimeException
{
    public function __construct(string $provider, string $actionType)
    {
        parent::__construct("El proveedor [{$provider}] no soporta la acción [{$actionType}].");
    }
}
