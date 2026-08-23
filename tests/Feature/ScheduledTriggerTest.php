<?php

namespace Tests\Feature;

use App\Jobs\ExecuteAutomation;
use App\Models\Automation;
use App\Models\AutomationExecution;
use App\Models\AutomationTrigger;
use App\Models\ProcessedEvent;
use App\Models\User;
use App\Services\ScheduledAutomationDispatcher;
use App\Services\ScheduledTriggerPlanner;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ScheduledTriggerTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_store_rejects_invalid_cron_for_scheduled_trigger(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('automations.index'))
            ->post(route('automations.store'), [
                'name' => 'Cron inválido',
                'is_active' => '1',
                'trigger' => [
                    'type' => 'schedule',
                    'cron_expression' => 'not-a-cron',
                    'timezone' => 'America/Costa_Rica',
                ],
                'actions' => [[
                    'type' => 'google.send_email',
                    'config' => [
                        'to' => 'test@example.com',
                        'subject' => 'Hola',
                        'body' => 'Mundo',
                    ],
                ]],
            ])
            ->assertRedirect(route('automations.index'))
            ->assertSessionHasErrors('trigger.cron_expression');
    }

    public function test_store_calculates_next_run_at_for_scheduled_trigger(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-08-23 15:00:00', 'America/Costa_Rica'));

        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('automations.index'))
            ->post(route('automations.store'), [
                'name' => 'Cada minuto',
                'is_active' => '1',
                'trigger' => [
                    'type' => 'schedule',
                    'cron_expression' => '* * * * *',
                    'timezone' => 'America/Costa_Rica',
                ],
                'actions' => [[
                    'type' => 'google.send_email',
                    'config' => [
                        'to' => 'test@example.com',
                        'subject' => 'Hola',
                        'body' => 'Mundo',
                    ],
                ]],
            ])
            ->assertRedirect(route('automations.index'));

        $trigger = AutomationTrigger::query()->first();

        $this->assertNotNull($trigger);
        $this->assertSame('schedule', $trigger->type);
        $this->assertSame('America/Costa_Rica', $trigger->timezone);
        $this->assertNotNull($trigger->next_run_at);
        $this->assertTrue($trigger->next_run_at->greaterThanOrEqualTo(now()->utc()));
    }

    public function test_dispatch_due_ignores_inactive_scheduled_automations(): void
    {
        Queue::fake();

        Carbon::setTestNow('2026-08-23 16:00:00');

        $dueAt = Carbon::parse('2026-08-23 16:00:00');
        $automation = $this->createScheduledAutomation(isActive: false, dueAt: $dueAt);

        Artisan::call('automations:dispatch-due');

        Queue::assertNothingPushed();
        $this->assertDatabaseCount('automation_executions', 0);
        $this->assertTrue($automation->trigger->fresh()->next_run_at->equalTo($dueAt));
    }

    public function test_dispatch_due_enqueues_execute_automation_and_advances_next_run_at(): void
    {
        Queue::fake();

        Carbon::setTestNow('2026-08-23 16:00:00');

        $automation = $this->createScheduledAutomation(
            cronExpression: '* * * * *',
            dueAt: Carbon::parse('2026-08-23 16:00:00'),
        );

        $previousRunAt = $automation->trigger->next_run_at->copy();

        Artisan::call('automations:dispatch-due');

        Queue::assertPushed(ExecuteAutomation::class, 1);

        $execution = AutomationExecution::query()->first();
        $this->assertNotNull($execution);
        $this->assertSame($automation->id, $execution->automation_id);
        $this->assertStringStartsWith('scheduled:'.$automation->id.':', $execution->event_key);

        $automation->trigger->refresh();
        $this->assertTrue($automation->trigger->next_run_at->greaterThan($previousRunAt));
    }

    public function test_duplicate_tick_does_not_enqueue_twice_for_same_occurrence(): void
    {
        Queue::fake();

        Carbon::setTestNow('2026-08-23 16:00:00');

        $automation = $this->createScheduledAutomation(
            cronExpression: '* * * * *',
            dueAt: Carbon::parse('2026-08-23 16:00:00'),
        );

        $dispatcher = app(ScheduledAutomationDispatcher::class);

        $this->assertTrue($dispatcher->dispatchAutomation($automation->fresh(['trigger', 'actions', 'conditions'])));
        $this->assertFalse($dispatcher->dispatchAutomation($automation->fresh(['trigger', 'actions', 'conditions'])));

        Queue::assertPushed(ExecuteAutomation::class, 1);
        $this->assertSame(1, ProcessedEvent::query()->count());
    }

    public function test_planner_computes_next_run_in_utc(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-08-23 15:30:00', 'America/Costa_Rica'));

        $next = app(ScheduledTriggerPlanner::class)->computeNextRunAt(
            '0 16 * * *',
            'America/Costa_Rica',
        );

        $this->assertSame('UTC', $next->timezone->getName());
        $this->assertSame('2026-08-23 22:00:00', $next->format('Y-m-d H:i:s'));
    }

    private function createScheduledAutomation(
        bool $isActive = true,
        string $cronExpression = '* * * * *',
        ?Carbon $dueAt = null,
    ): Automation {
        $user = User::factory()->create();

        $automation = Automation::factory()->create([
            'user_id' => $user->id,
            'is_active' => $isActive,
        ]);

        $automation->trigger()->create([
            'type' => 'schedule',
            'cron_expression' => $cronExpression,
            'timezone' => 'UTC',
            'next_run_at' => ($dueAt ?? now())->copy()->utc(),
        ]);

        $automation->actions()->create([
            'type' => 'google.send_email',
            'position' => 0,
            'config' => [
                'to' => 'owner@example.com',
                'subject' => 'Programado',
                'body' => 'Hola',
            ],
        ]);

        return $automation->fresh(['trigger', 'actions', 'conditions']);
    }
}
