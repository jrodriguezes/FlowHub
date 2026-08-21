<?php

namespace App\Services;

use App\Contracts\ProviderAdapter;
use App\DTO\ActionResult;
use App\Models\ServiceConnection;
use Illuminate\Contracts\Container\Container;

class ProviderManager
{
    /**
     * @param  array<string, class-string<ProviderAdapter>>  $providers
     */
    public function __construct(
        private Container $container,
        private array $providers = [],
    ) {}

    /**
     * @param  class-string<ProviderAdapter>  $adapterClass
     */
    public function extend(string $provider, string $adapterClass): void
    {
        $this->providers[$provider] = $adapterClass;
    }

    public function adapterFor(string $provider): ProviderAdapter
    {
        $adapterClass = $this->providers[$provider] ?? null;

        if ($adapterClass === null) {
            throw new \RuntimeException("No hay un adaptador registrado para el proveedor [{$provider}].");
        }

        return $this->container->make($adapterClass);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function execute(
        string $provider,
        string $actionType,
        array $parameters,
        ServiceConnection $connection,
    ): ActionResult {
        return $this->adapterFor($provider)->execute($actionType, $parameters, $connection);
    }

    /**
     * @return list<string>
     */
    public function registeredProviders(): array
    {
        return array_keys($this->providers);
    }
}
