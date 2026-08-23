<?php

namespace App\Jobs;

use App\Models\ExecutionStep;
use App\Services\ExecutionLogger;
use App\Services\ProviderManager;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessAutomationAction implements ShouldQueue
{
    use Queueable;

    public int $tries = 4;

    public int $timeout = 120;

    public function __construct(public readonly int $executionStepId)
    {
        $this->onQueue('automations');
    }

    /**
     * @return list<int>
     */
    public function backoff(): array
    {
        return [10, 30, 120];
    }

    public function handle(ProviderManager $providerManager, ExecutionLogger $logger): void
    {
        $step = ExecutionStep::with(['execution', 'action.serviceConnection'])->find($this->executionStepId);

        if (!$step) {
            return;
        }

        $logger->markStepProcessing($step);

        try {
            $provider = explode('.', $step->action->type)[0];
            $payload = $step->execution->input_payload ?? [];
            $config = $step->action->config ?? [];
            $config = $this->interpolateConfig($config, $payload);

            $result = $providerManager->execute(
                $provider,
                $step->action->type,
                $config,
                $step->action->serviceConnection,
            );

            if ($result->success) {
                $logger->markStepSuccessful($step, $result->data ?? []);
            } else {
                $logger->markStepFailed($step, (string) ($result->error ?? 'La acción devolvió un error.'));
            }
        } catch (\Throwable $e) {
            Log::error('Job Failed: '.$e->getMessage());

            $message = config('app.debug')
                ? $e->getMessage()
                : 'Ocurrió un error al ejecutar la acción.';

            $logger->markStepFailed($step, $message);

            throw $e;
        }
    }

    private function interpolateConfig(array $config, array $payload): array
    {
        $interpolated = [];

        foreach ($config as $key => $value) {
            if (is_string($value)) {
                $interpolated[$key] = preg_replace_callback('/\$\{trigger\.([a-zA-Z0-9_\.]+)\}/', function ($matches) use ($payload) {
                    $path = explode('.', $matches[1]);
                    $current = $payload;

                    foreach ($path as $segment) {
                        if (is_array($current) && array_key_exists($segment, $current)) {
                            $current = $current[$segment];
                        } else {
                            return $matches[0];
                        }
                    }

                    return is_scalar($current) ? (string) $current : json_encode($current);
                }, $value);
            } elseif (is_array($value)) {
                $interpolated[$key] = $this->interpolateConfig($value, $payload);
            } else {
                $interpolated[$key] = $value;
            }
        }

        return $interpolated;
    }
}
