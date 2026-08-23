<?php

namespace App\Services;

use App\Enums\ExecutionStatus;
use App\Jobs\ExecuteAutomation;
use App\Models\Automation;
use App\Models\AutomationExecution;
use App\Models\ExecutionStep;

class ExecutionEngine
{
    public function __construct(
        private readonly ConditionEvaluator $evaluator,
        private readonly ExecutionPayloadSanitizer $sanitizer,
        private readonly ExecutionLogger $logger,
    ) {
    }

    public function process(Automation $automation, array $payload): void
    {
        $sanitizedPayload = $this->sanitizer->sanitize($payload) ?? [];
        $conditionsArray = $automation->conditions ? $automation->conditions->toArray() : [];

        if (!$this->evaluator->evaluate($conditionsArray, $payload)) {
            $execution = AutomationExecution::create([
                'user_id' => $automation->user_id,
                'automation_id' => $automation->id,
                'event_key' => (string) ($payload['idempotency_key'] ?? 'skipped:'.$automation->id.':'.now()->timestamp),
                'status' => ExecutionStatus::PENDING,
                'input_payload' => $sanitizedPayload,
            ]);

            $this->logger->markExecutionSkipped($execution, $sanitizedPayload);

            return;
        }

        $idempotencyKey = (string) (
            $payload['idempotency_key']
            ?? $payload['github_delivery']
            ?? $payload['delivery_id']
            ?? 'execution:'.$automation->id.':'.now()->timestamp
        );

        $execution = AutomationExecution::create([
            'user_id' => $automation->user_id,
            'automation_id' => $automation->id,
            'event_key' => $idempotencyKey,
            'status' => ExecutionStatus::PENDING,
            'input_payload' => $sanitizedPayload,
        ]);

        foreach ($automation->actions as $index => $action) {
            ExecutionStep::create([
                'automation_execution_id' => $execution->id,
                'automation_action_id' => $action->id,
                'position' => $index,
                'status' => ExecutionStatus::PENDING,
                'input_payload' => $this->sanitizer->sanitize($action->config ?? []),
            ]);
        }

        ExecuteAutomation::dispatch($execution->id, $idempotencyKey)->afterCommit();
    }
}
