<?php

namespace App\Contracts;

use App\DTO\ActionResult;
use App\Models\ServiceConnection;

interface ProviderAdapter
{
    /**
     * @param  array<string, mixed>  $parameters
     */
    public function execute(
        string $actionType,
        array $parameters,
        ServiceConnection $connection,
    ): ActionResult;

    /**
     * @return list<string>
     */
    public function supportedActions(): array;
}
