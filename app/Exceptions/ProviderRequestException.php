<?php

namespace App\Exceptions;

use RuntimeException;

class ProviderRequestException extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly int $statusCode = 0,
        public readonly bool $retryable = false,
        public readonly ?int $retryAfterSeconds = null,
        public readonly string $provider = '',
    ) {
        parent::__construct($message);
    }
}
