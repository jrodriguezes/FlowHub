<?php

namespace App\Adapters;

use App\Contracts\ProviderAdapter;
use App\DTO\ActionResult;
use App\Enums\ConnectionStatus;

use App\Models\ServiceConnection;

abstract class AbstractProviderAdapter implements ProviderAdapter
{
    abstract public function provider(): string;

    public function execute(string $actionType, array $parameters, ServiceConnection $connection): ActionResult
    {
        $this->assertCompatibleConnection($connection);

        if (!in_array($actionType, $this->supportedActions(), true)) {
            throw new \RuntimeException("La acción '{$actionType}' no está soportada por el proveedor [{$this->provider()}].");
        }

        return $this->perform($actionType, $parameters, $connection);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    abstract protected function perform(
        string $actionType,
        array $parameters,
        ServiceConnection $connection,
    ): ActionResult;

    protected function assertCompatibleConnection(ServiceConnection $connection): void
    {
        if ($connection->provider !== $this->provider()) {
            throw new \RuntimeException("La conexión no es válida para el proveedor [{$this->provider()}]: proveedor distinto.");
        }

        if ($connection->status !== ConnectionStatus::ACTIVE) {
            throw new \RuntimeException("La conexión no es válida para el proveedor [{$this->provider()}]: conexión inactiva o revocada.");
        }
    }
}
