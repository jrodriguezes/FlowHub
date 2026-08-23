<?php

namespace App\Jobs;

use App\Enums\ExecutionStatus;
use App\Models\AutomationExecution;
use App\Models\ExecutionStep;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ExecuteAutomation implements ShouldQueue
{
    use Queueable;

    public int $tries = 4;

    public int $timeout = 120;

    public function __construct(
        public readonly int $automationExecutionId,
        public readonly string $idempotencyKey,
    ) {
        $this->onQueue('automations');
    }

    /**
     * @return list<int>
     */
    public function backoff(): array
    {
        return [10, 30, 120];
    }

    public function handle(): void
    {
        Log::info('ExecuteAutomation consumido desde la cola Redis.', [
            'automation_execution_id' => $this->automationExecutionId,
            'idempotency_key' => $this->idempotencyKey,
        ]);

        $execution = AutomationExecution::query()->find($this->automationExecutionId);

        if (!$execution) {
            return;
        }

        $execution->update(['status' => ExecutionStatus::PROCESSING]);

        ExecutionStep::query()
            ->where('automation_execution_id', $execution->id)
            ->orderBy('position')
            ->pluck('id')
            ->each(fn (int $stepId) => ProcessAutomationAction::dispatch($stepId));
    }
}
