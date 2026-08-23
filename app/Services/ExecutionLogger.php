<?php

namespace App\Services;

use App\Enums\ExecutionStatus;
use App\Models\AutomationExecution;
use App\Models\ExecutionStep;

class ExecutionLogger
{
    public function __construct(
        private readonly ExecutionPayloadSanitizer $sanitizer,
    ) {
    }

    public function markExecutionPending(AutomationExecution $execution): void
    {
        $execution->update([
            'status' => ExecutionStatus::PENDING,
        ]);
    }

    public function markExecutionProcessing(AutomationExecution $execution): void
    {
        $execution->update([
            'status' => ExecutionStatus::PROCESSING,
            'started_at' => $execution->started_at ?? now(),
        ]);
    }

    public function markExecutionSkipped(AutomationExecution $execution, array $payload): void
    {
        $execution->update([
            'status' => ExecutionStatus::SKIPPED,
            'input_payload' => $this->sanitizer->sanitize($payload),
            'completed_at' => now(),
            'error_details' => [
                'message' => 'La ejecución no cumplió las condiciones configuradas.',
            ],
        ]);
    }

    public function markExecutionSuccessful(AutomationExecution $execution, ?array $output = null): void
    {
        $execution->update([
            'status' => ExecutionStatus::SUCCESSFUL,
            'output_payload' => $this->sanitizer->sanitize($output),
            'completed_at' => now(),
            'error_details' => null,
        ]);
    }

    public function markExecutionFailed(AutomationExecution $execution, string $message): void
    {
        $execution->update([
            'status' => ExecutionStatus::FAILED,
            'completed_at' => now(),
            'error_details' => [
                'message' => $this->sanitizer->sanitizeString($message),
            ],
        ]);
    }

    public function markStepProcessing(ExecutionStep $step): void
    {
        $step->update([
            'status' => ExecutionStatus::PROCESSING,
            'attempts' => $step->attempts + 1,
            'started_at' => $step->started_at ?? now(),
        ]);
    }

    public function markStepSuccessful(ExecutionStep $step, array $output): void
    {
        $step->update([
            'status' => ExecutionStatus::SUCCESSFUL,
            'completed_at' => now(),
            'output_payload' => $this->sanitizer->sanitize($output),
            'error_details' => null,
        ]);

        $execution = $step->execution()->with('steps')->first();

        if ($execution) {
            $this->refreshExecutionStatus($execution);
        }
    }

    public function markStepFailed(ExecutionStep $step, string $message): void
    {
        $step->update([
            'status' => ExecutionStatus::FAILED,
            'completed_at' => now(),
            'error_details' => [
                'message' => $this->sanitizer->sanitizeString($message),
            ],
        ]);

        $execution = $step->execution()->with('steps')->first();

        if ($execution) {
            $this->refreshExecutionStatus($execution);
        }
    }

    public function refreshExecutionStatus(AutomationExecution $execution): void
    {
        $execution->load('steps');

        if ($execution->steps->contains(fn (ExecutionStep $step) => $step->status === ExecutionStatus::FAILED)) {
            $failedStep = $execution->steps->first(fn (ExecutionStep $step) => $step->status === ExecutionStatus::FAILED);
            $message = $failedStep?->error_details['message'] ?? 'Una acción falló durante la ejecución.';

            $this->markExecutionFailed($execution, $message);

            return;
        }

        $pendingOrProcessing = $execution->steps->contains(
            fn (ExecutionStep $step) => in_array($step->status, [ExecutionStatus::PENDING, ExecutionStatus::PROCESSING], true),
        );

        if ($pendingOrProcessing) {
            $this->markExecutionProcessing($execution);

            return;
        }

        if ($execution->steps->isNotEmpty() && $execution->steps->every(
            fn (ExecutionStep $step) => $step->status === ExecutionStatus::SUCCESSFUL,
        )) {
            $this->markExecutionSuccessful(
                $execution,
                $execution->steps->pluck('output_payload')->filter()->values()->all(),
            );
        }
    }
}
