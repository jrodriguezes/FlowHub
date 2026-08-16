<?php

namespace App\Exceptions;

use RuntimeException;

class IncompatibleConnectionException extends RuntimeException
{
    public function __construct(string $expectedProvider, string $reason)
    {
        parent::__construct("La conexión no es válida para [{$expectedProvider}]: {$reason}.");
    }
}
