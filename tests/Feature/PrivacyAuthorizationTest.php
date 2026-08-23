<?php

namespace Tests\Feature;

use App\Models\Automation;
use App\Models\AutomationExecution;
use App\Models\ServiceConnection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrivacyAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_private_resource_routes(): void
    {
        $this->get(route('automations.index'))->assertRedirect(route('login'));
        $this->get(route('connections.index'))->assertRedirect(route('login'));
        $this->get(route('executions.index'))->assertRedirect(route('login'));
    }

    public function test_user_can_list_only_own_automations(): void
    {
        [$owner, $stranger] = User::factory()->count(2)->create();
        Automation::factory()->create(['user_id' => $owner->id, 'name' => 'Own automation']);
        Automation::factory()->create(['user_id' => $stranger->id, 'name' => 'Foreign automation']);

        $response = $this->actingAs($owner)->get(route('automations.index'));

        $response->assertOk();
        $response->assertSee('Own automation');
        $response->assertDontSee('Foreign automation');
    }

    public function test_user_can_view_update_and_delete_own_automation(): void
    {
        $owner = User::factory()->create();
        $automation = $this->createAutomationFor($owner, 'Original automation');

        $this->actingAs($owner)
            ->get(route('automations.show', $automation))
            ->assertOk()
            ->assertSee('Original automation');

        $this->actingAs($owner)
            ->put(route('automations.update', $automation), $this->automationPayload('Updated automation'))
            ->assertRedirect(route('automations.index'));

        $this->assertDatabaseHas('automations', [
            'id' => $automation->id,
            'name' => 'Updated automation',
        ]);

        $this->actingAs($owner)
            ->delete(route('automations.destroy', $automation))
            ->assertRedirect(route('automations.index'));

        $this->assertDatabaseMissing('automations', ['id' => $automation->id]);
    }

    public function test_user_cannot_view_update_or_delete_foreign_automation(): void
    {
        [$owner, $stranger] = User::factory()->count(2)->create();
        $automation = $this->createAutomationFor($owner, 'Protected automation');

        $this->actingAs($stranger)
            ->get(route('automations.show', $automation))
            ->assertForbidden();

        $this->actingAs($stranger)
            ->put(route('automations.update', $automation), $this->automationPayload('Hijacked'))
            ->assertForbidden();

        $this->actingAs($stranger)
            ->delete(route('automations.destroy', $automation))
            ->assertForbidden();

        $this->assertDatabaseHas('automations', [
            'id' => $automation->id,
            'name' => 'Protected automation',
        ]);
    }

    public function test_user_can_view_connections_without_exposing_tokens(): void
    {
        [$owner, $stranger] = User::factory()->count(2)->create();
        ServiceConnection::factory()->create([
            'user_id' => $owner->id,
            'provider' => 'github',
            'access_token' => 'secret-access',
            'refresh_token' => 'secret-refresh',
        ]);
        ServiceConnection::factory()->create(['user_id' => $stranger->id, 'provider' => 'google']);

        $response = $this->actingAs($owner)->get(route('connections.index'));

        $response->assertOk();
        $response->assertSee('GitHub');
        $response->assertDontSee('secret-access', false);
        $response->assertDontSee('secret-refresh', false);
    }

    public function test_user_cannot_revoke_foreign_connection(): void
    {
        [$owner, $stranger] = User::factory()->count(2)->create();
        $connection = ServiceConnection::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($stranger)
            ->delete(route('connections.destroy', $connection))
            ->assertForbidden();

        $this->assertDatabaseHas('service_connections', ['id' => $connection->id]);
    }

    public function test_user_can_revoke_own_connection(): void
    {
        $owner = User::factory()->create();
        $connection = ServiceConnection::factory()->create([
            'user_id' => $owner->id,
            'provider' => 'github',
            'access_token' => 'secret-access',
            'refresh_token' => 'secret-refresh',
        ]);

        $this->actingAs($owner)
            ->from(route('connections.index'))
            ->delete(route('connections.destroy', $connection))
            ->assertRedirect(route('connections.index'));

        $connection->refresh();

        $this->assertSame('revoked', $connection->status->value);
        $this->assertNull($connection->access_token);
        $this->assertNotNull($connection->revoked_at);
    }

    public function test_user_can_list_only_own_executions(): void
    {
        [$owner, $stranger] = User::factory()->count(2)->create();
        $ownAutomation = Automation::factory()->create([
            'user_id' => $owner->id,
            'name' => 'Own execution automation',
        ]);
        $foreignAutomation = Automation::factory()->create([
            'user_id' => $stranger->id,
            'name' => 'Foreign execution automation',
        ]);

        $own = AutomationExecution::factory()->forOwner($owner, $ownAutomation)->create();
        AutomationExecution::factory()->forOwner($stranger, $foreignAutomation)->create();

        $response = $this->actingAs($owner)->get(route('executions.index'));

        $response->assertOk();
        $response->assertSee('#'.$own->id);
        $response->assertSee('Own execution automation');
        $response->assertDontSee('Foreign execution automation');
    }

    public function test_user_cannot_view_foreign_execution(): void
    {
        [$owner, $stranger] = User::factory()->count(2)->create();
        $automation = Automation::factory()->create(['user_id' => $owner->id]);
        $execution = AutomationExecution::factory()->forOwner($owner, $automation)->create();

        $this->actingAs($stranger)
            ->get(route('executions.show', $execution))
            ->assertForbidden();

        $this->assertDatabaseHas('automation_executions', ['id' => $execution->id]);
    }

    public function test_user_can_view_own_execution(): void
    {
        $owner = User::factory()->create();
        $automation = Automation::factory()->create(['user_id' => $owner->id, 'name' => 'Demo automation']);
        $execution = AutomationExecution::factory()->forOwner($owner, $automation)->create();

        $this->actingAs($owner)
            ->get(route('executions.show', $execution))
            ->assertOk()
            ->assertSee('Detalle de Ejecución #'.$execution->id)
            ->assertSee('Demo automation');
    }

    /**
     * @return array<string, mixed>
     */
    private function automationPayload(string $name): array
    {
        return [
            'name' => $name,
            'description' => 'Automation used in authorization tests',
            'is_active' => true,
            'trigger' => ['type' => 'github_issue'],
            'actions' => [[
                'type' => 'google.send_email',
                'config' => [
                    'to' => 'owner@example.com',
                    'subject' => 'Subject',
                    'body' => 'Body',
                ],
            ]],
        ];
    }

    private function createAutomationFor(User $owner, string $name): Automation
    {
        $automation = Automation::factory()->create([
            'user_id' => $owner->id,
            'name' => $name,
        ]);

        $automation->trigger()->create(['type' => 'github_issue']);
        $automation->actions()->create([
            'type' => 'google.send_email',
            'position' => 0,
            'config' => [
                'to' => 'owner@example.com',
                'subject' => 'Subject',
                'body' => 'Body',
            ],
        ]);

        return $automation;
    }
}
