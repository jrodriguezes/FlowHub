<?php

namespace App\Exceptions;

use RuntimeException;

class UnsupportedProviderException extends RuntimeException
{
    public function __construct(string $provider)
    {
        parent::__construct("No hay un adaptador registrado para el proveedor [{$provider}].");
    }
}
