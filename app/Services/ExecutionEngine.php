<?php

namespace App\Services;

use App\Enums\ExecutionStatus;
use App\Jobs\ProcessAutomationAction;
use App\Models\Automation;
use App\Models\AutomationExecution;
use App\Models\ExecutionStep;

class ExecutionEngine
{
    public function __construct(
        private readonly ConditionEvaluator $evaluator
    ) {
    }

    public function process(Automation $automation, array $payload): void
    {
        // we check whether the conditions are met (example. branch == ‘main’)
        if (!$this->evaluator->evaluate($automation->conditions ?? [], $payload)) {
            // if this condition is not met, we abort and do nothing
            return;
        }

        // if pass the evaluation, we'll save in the db that the execution started
        $execution = AutomationExecution::create([
            'user_id' => $automation->user_id,
            'automation_id' => $automation->id,
            'status' => ExecutionStatus::PENDING,
            'payload' => $payload,
        ]);

        // we iterate on each action declared in the automation
        foreach ($automation->actions as $action) {
            // we declare the step in the db
            $step = ExecutionStep::create([
                'automation_execution_id' => $execution->id,
                'action' => $action,
                'status' => ExecutionStatus::PENDING,
            ]);

            // instead of execute it, we dispatch the job to redis
            ProcessAutomationAction::dispatch($step->id);
        }
    }
}