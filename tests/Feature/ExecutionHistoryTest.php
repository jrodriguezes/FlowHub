<?php

namespace Tests\Feature;

use App\Models\Automation;
use App\Models\AutomationExecution;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExecutionHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_execution_index_filters_by_status(): void
    {
        $owner = User::factory()->create();
        $automation = Automation::factory()->create(['user_id' => $owner->id]);
        $failed = AutomationExecution::factory()->forOwner($owner, $automation)->failed()->create();
        AutomationExecution::factory()->forOwner($owner, $automation)->successful()->create();

        $response = $this->actingAs($owner)->get(route('executions.index', ['status' => 'failed']));

        $response->assertOk();
        $response->assertSee('#'.$failed->id);
        $response->assertSee('Fallido');
        $this->assertCount(1, $response->viewData('executions'));
    }

    public function test_execution_index_filters_by_automation(): void
    {
        $owner = User::factory()->create();
        $firstAutomation = Automation::factory()->create(['user_id' => $owner->id, 'name' => 'Alpha flow']);
        $secondAutomation = Automation::factory()->create(['user_id' => $owner->id, 'name' => 'Beta flow']);
        $target = AutomationExecution::factory()->forOwner($owner, $firstAutomation)->create();
        AutomationExecution::factory()->forOwner($owner, $secondAutomation)->create();

        $response = $this->actingAs($owner)->get(route('executions.index', [
            'automation_id' => $firstAutomation->id,
        ]));

        $response->assertOk();
        $response->assertSee('#'.$target->id);
        $response->assertSee('Alpha flow');
        $executions = $response->viewData('executions');

        $this->assertCount(1, $executions);
        $this->assertSame($target->id, $executions->items()[0]->id);
    }

    public function test_execution_index_filters_by_date_range(): void
    {
        $owner = User::factory()->create();
        $automation = Automation::factory()->create(['user_id' => $owner->id]);
        $inRange = AutomationExecution::factory()->forOwner($owner, $automation)->create([
            'created_at' => now()->subDays(2),
        ]);
        AutomationExecution::factory()->forOwner($owner, $automation)->create([
            'created_at' => now()->subDays(10),
        ]);

        $response = $this->actingAs($owner)->get(route('executions.index', [
            'from' => now()->subDays(3)->toDateString(),
            'to' => now()->toDateString(),
        ]));

        $response->assertOk();
        $response->assertSee('#'.$inRange->id);
        $this->assertCount(1, $response->viewData('executions'));
    }

    public function test_execution_index_is_paginated(): void
    {
        $owner = User::factory()->create();
        $automation = Automation::factory()->create(['user_id' => $owner->id]);
        AutomationExecution::factory()->count(16)->forOwner($owner, $automation)->create();

        $response = $this->actingAs($owner)->get(route('executions.index'));

        $response->assertOk();
        $this->assertCount(15, $response->viewData('executions'));
        $response->assertSee('page=2', false);
    }

    public function test_execution_show_displays_skipped_and_failed_states(): void
    {
        $owner = User::factory()->create();
        $automation = Automation::factory()->create(['user_id' => $owner->id, 'name' => 'Demo flow']);

        $skipped = AutomationExecution::factory()->forOwner($owner, $automation)->skipped()->create();
        $failed = AutomationExecution::factory()->forOwner($owner, $automation)->failed()->create();

        $this->actingAs($owner)
            ->get(route('executions.show', $skipped))
            ->assertOk()
            ->assertSee('Omitido')
            ->assertSee('Condiciones no cumplidas');

        $this->actingAs($owner)
            ->get(route('executions.show', $failed))
            ->assertOk()
            ->assertSee('Fallido')
            ->assertSee('Error simulado');
    }

    public function test_execution_show_never_renders_sensitive_tokens(): void
    {
        $owner = User::factory()->create();
        $automation = Automation::factory()->create(['user_id' => $owner->id]);
        $execution = AutomationExecution::factory()->forOwner($owner, $automation)->create([
            'input_payload' => [
                'access_token' => 'ghp_superSecretToken1234567890',
                'authorization' => 'Bearer abc.def.ghi',
            ],
        ]);

        $response = $this->actingAs($owner)->get(route('executions.show', $execution));

        $response->assertOk();
        $response->assertDontSee('ghp_superSecretToken1234567890', false);
        $response->assertDontSee('Bearer abc.def.ghi', false);
        $response->assertSee('[redacted]', false);
    }
}
