<?php

namespace App\Services;

use App\Models\Automation;
use App\Models\AutomationTrigger;
use App\Models\ProcessedEvent;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class ScheduledAutomationDispatcher
{
    public function __construct(
        private readonly ScheduledTriggerPlanner $planner,
        private readonly ExecutionEngine $engine,
    ) {
    }

    /**
     * @return int Cantidad de automatizaciones encoladas.
     */
    public function dispatchDue(): int
    {
        $due = Automation::query()
            ->where('is_active', true)
            ->whereHas('trigger', function ($query) {
                $query->where('type', 'schedule')
                    ->whereNotNull('cron_expression')
                    ->whereNotNull('next_run_at')
                    ->where('next_run_at', '<=', now());
            })
            ->with(['trigger', 'actions', 'conditions'])
            ->get();

        $dispatched = 0;

        foreach ($due as $automation) {
            if ($this->dispatchAutomation($automation)) {
                $dispatched++;
            }
        }

        return $dispatched;
    }

    public function dispatchAutomation(Automation $automation): bool
    {
        return (bool) DB::transaction(function () use ($automation) {
            /** @var AutomationTrigger|null $trigger */
            $trigger = AutomationTrigger::query()
                ->where('automation_id', $automation->id)
                ->lockForUpdate()
                ->first();

            if (
                !$trigger
                || $trigger->type !== 'schedule'
                || blank($trigger->cron_expression)
                || !$trigger->next_run_at
                || $trigger->next_run_at->isFuture()
            ) {
                return false;
            }

            $scheduledAtUtc = $trigger->next_run_at->copy()->utc();
            $idempotencyKey = sprintf(
                'scheduled:%d:%s',
                $automation->id,
                $scheduledAtUtc->format('Y-m-d\TH:i:s\Z'),
            );

            if (!$this->reserveOccurrence($idempotencyKey)) {
                return false;
            }

            $timezone = $trigger->timezone ?: 'UTC';
            $nextRunAt = $this->planner->computeNextRunAt(
                $trigger->cron_expression,
                $timezone,
                $scheduledAtUtc->copy()->timezone($timezone),
            );

            $trigger->update(['next_run_at' => $nextRunAt]);

            $payload = [
                'trigger' => [
                    'type' => 'schedule',
                    'scheduled_at' => $scheduledAtUtc->toIso8601String(),
                    'cron_expression' => $trigger->cron_expression,
                    'timezone' => $timezone,
                ],
                'idempotency_key' => $idempotencyKey,
            ];

            $this->engine->process(
                $automation->fresh(['actions', 'conditions']),
                $payload,
            );

            return true;
        });
    }

    private function reserveOccurrence(string $idempotencyKey): bool
    {
        try {
            ProcessedEvent::query()->create([
                'idempotency_key' => $idempotencyKey,
                'status' => 'reserved',
            ]);

            return true;
        } catch (QueryException $exception) {
            if ($this->isUniqueViolation($exception)) {
                return false;
            }

            throw $exception;
        }
    }

    private function isUniqueViolation(QueryException $exception): bool
    {
        $sqlState = $exception->errorInfo[0] ?? null;

        return in_array($sqlState, ['23505', '23000'], true);
    }
}
