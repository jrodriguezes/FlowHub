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

            $result = $providerManager->execute(
                $provider,
                $step->action->type,
                $step->action->config ?? [],
                $step->action->serviceConnection,
            );

            // if it finished successfully, we save the success message and the data returned by google/github
            if ($result->isSuccess()) {
                $step->update([
                    'status' => ExecutionStatus::SUCCESSFUL,
                    'completed_at' => now(),
                    'result_data' => $result->data, // we save the ThreadID or EventID
                ]);
            } else {
                // if it failed, we save the error message
                $step->update([
                    'status' => ExecutionStatus::FAILED,
                    'completed_at' => now(),
                    'error_message' => $result->message,
                ]);
            }
        } catch (\Throwable $e) {
            $step->update([
                'status' => ExecutionStatus::FAILED,
                'completed_at' => now(),
                'error_message' => clone_exception_message($e),
            ]);
            throw $e;
        }
    }
}
