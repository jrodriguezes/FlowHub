<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\ExecutionStep;
use App\Services\ProviderManager;
use App\Enums\ExecutionStatus;

class ProcessAutomationAction implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    // here we receive the id that we are going to process
    public function __construct(public readonly int $executionStepId)
    {
    }

    public function handle(ProviderManager $providerManager): void
    {
        // we search the step in the bd
        $step = ExecutionStep::with(['execution', 'action.serviceConnection'])->find($this->executionStepId);

        if (!$step) {
            return; // if the step isn't found, we will do nothing
        }

        // we mark as processing using the enum 
        $step->update([
            'status' => ExecutionStatus::PROCESSING,
            'started_at' => now(),
        ]);

        try {
            // we extract "google" from "google.send_email"
            $provider = explode('.', $step->action->type)[0];
            
            $payload = $step->execution->payload ?? [];
            $config = $step->action->config ?? [];
            
            // Interpolate variables
            $config = $this->interpolateConfig($config, $payload);

            $result = $providerManager->execute(
                $provider,
                $step->action->type,
                $config,
                $step->action->serviceConnection,
            );

            // if it finished successfully, we save the success message and the data returned by google/github
            if ($result->success) {
                $step->update([
                    'status' => ExecutionStatus::SUCCESSFUL,
                    'completed_at' => now(),
                    'output_payload' => $result->data,
                ]);
            } else {
                // if it failed, we save the error message
                $step->update([
                    'status' => ExecutionStatus::FAILED,
                    'completed_at' => now(),
                    'error_details' => ['message' => $result->error],
                ]);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Job Failed: ' . $e->getMessage() . ' - ' . $e->getTraceAsString());
            $step->update([
                'status' => ExecutionStatus::FAILED,
                'completed_at' => now(),
                'error_details' => ['message' => $e->getMessage()],
            ]);
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
                            return $matches[0]; // Not found, keep original text
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
