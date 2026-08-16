<?php

namespace App\DTO;

final readonly class ActionResult
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function __construct(
        public bool $success,
        public string $provider,
        public ?string $externalId = null,
        public array $data = [],
        public ?string $error = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function ok(string $provider, ?string $externalId = null, array $data = []): self
    {
        return new self(
            success: true,
            provider: $provider,
            externalId: $externalId,
            data: $data,
        );
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function failure(string $provider, string $error, array $data = []): self
    {
        return new self(
            success: false,
            provider: $provider,
            data: $data,
            error: $error,
        );
    }
}
