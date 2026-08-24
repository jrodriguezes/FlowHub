<?php

namespace App\Jobs;

use App\Enums\ExecutionStatus;
use App\Models\AutomationExecution;
use App\Models\ExecutionStep;
use App\Services\ExecutionLogger;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

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

    public function handle(ExecutionLogger $logger): void
    {
        Log::info('ExecuteAutomation consumido desde la cola Redis.', [
            'automation_execution_id' => $this->automationExecutionId,
            'idempotency_key' => $this->idempotencyKey,
        ]);

        $execution = AutomationExecution::query()->find($this->automationExecutionId);

        if (!$execution || $execution->status->value === 'skipped') {
            return;
        }

        $logger->markExecutionProcessing($execution);

        ExecutionStep::query()
            ->where('automation_execution_id', $execution->id)
            ->orderBy('position')
            ->pluck('id')
            ->each(fn(int $stepId) => ProcessAutomationAction::dispatch($stepId));
    }

    public function failed(\Throwable $exception): void
    {
        $execution = AutomationExecution::find($this->automationExecutionId);
        if (!$execution)
            return;

        // 1. Anotamos que todo el proceso falló
        $execution->update([
            'status' => ExecutionStatus::FAILED,
            'error_details' => json_encode(['error' => 'Error crítico en el motor: ' . $exception->getMessage()]),
        ]);

        // 2. Fotocopia para la UI en la BD
        DB::table('dead_letter_messages')->insert([
            'automation_execution_id' => $execution->id,
            'payload' => json_encode(['error_message' => 'Fallo maestro: ' . $exception->getMessage()]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Cuarto oscuro (DLQ) en Redis
        Redis::connection()->rpush(
            'queues:automation-dead-letter',
            json_encode(['type' => 'master_dead_letter', 'execution_id' => $execution->id, 'error' => $exception->getMessage()])
        );
    }

}
