<?php

namespace Tests\Feature;

use App\Jobs\ExecuteAutomation;
use App\Jobs\ProcessAutomationAction;
use App\Models\Automation;
use App\Models\AutomationExecution;
use App\Models\ExecutionStep;
use App\Models\User;
use App\Services\ExecutionEngine;
use App\Services\ExecutionLogger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ExecuteAutomationQueueTest extends TestCase
{
    use RefreshDatabase;

    public function test_env_example_uses_redis_queue_and_automations_queue_name(): void
    {
        $envExample = file_get_contents(base_path('.env.example'));

        $this->assertStringContainsString('QUEUE_CONNECTION=redis', $envExample);
        $this->assertStringContainsString('REDIS_QUEUE=automations', $envExample);
        $this->assertStringNotContainsString('QUEUE_CONNECTION=database', $envExample);
        $this->assertStringNotContainsString('QUEUE_CONNECTION=sync', $envExample);
    }

    public function test_execution_engine_publishes_a_single_execute_automation_job(): void
    {
        Queue::fake();

        $automation = $this->createRunnableAutomation();

        app(ExecutionEngine::class)->process($automation, [
            'issue' => ['title' => 'Urgente'],
            'idempotency_key' => 'github:delivery-123:automation:'.$automation->id,
        ]);

        Queue::assertPushed(ExecuteAutomation::class, 1);
        Queue::assertPushed(ExecuteAutomation::class, function (ExecuteAutomation $job) use ($automation) {
            $execution = AutomationExecution::query()->first();

            return $execution !== null
                && $execution->automation_id === $automation->id
                && $job->automationExecutionId === $execution->id
                && $job->idempotencyKey === 'github:delivery-123:automation:'.$automation->id
                && $job->queue === 'automations';
        });

        Queue::assertNotPushed(ProcessAutomationAction::class);

        $this->assertDatabaseHas('automation_executions', [
            'automation_id' => $automation->id,
            'event_key' => 'github:delivery-123:automation:'.$automation->id,
        ]);
    }

    public function test_execute_automation_job_consumes_and_dispatches_step_jobs(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $automation = $this->createRunnableAutomation($user);
        $execution = AutomationExecution::factory()->forOwner($user, $automation)->create([
            'event_key' => 'test-key',
        ]);

        $step = ExecutionStep::query()->create([
            'automation_execution_id' => $execution->id,
            'automation_action_id' => $automation->actions()->first()->id,
            'position' => 0,
            'status' => 'pending',
        ]);

        (new ExecuteAutomation($execution->id, 'test-key'))->handle(app(ExecutionLogger::class));

        Queue::assertPushed(ProcessAutomationAction::class, function (ProcessAutomationAction $job) use ($step) {
            return $job->executionStepId === $step->id && $job->queue === 'automations';
        });
    }

    private function createRunnableAutomation(?User $user = null): Automation
    {
        $user ??= User::factory()->create();

        $automation = Automation::factory()->create([
            'user_id' => $user->id,
            'is_active' => true,
        ]);

        $automation->trigger()->create(['type' => 'github_issue']);
        $automation->actions()->create([
            'type' => 'google.send_email',
            'position' => 0,
            'config' => [
                'to' => 'owner@example.com',
                'subject' => 'Alerta',
                'body' => 'Hola',
            ],
        ]);

        return $automation;
    }
}
