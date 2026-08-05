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
        $own = Automation::factory()->create(['user_id' => $owner->id, 'name' => 'Own automation']);
        Automation::factory()->create(['user_id' => $stranger->id, 'name' => 'Foreign automation']);

        $response = $this->actingAs($owner)->getJson(route('automations.index'));

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.id', $own->id);
        $response->assertJsonMissing(['name' => 'Foreign automation']);
    }

    public function test_user_can_view_update_and_delete_own_automation(): void
    {
        $owner = User::factory()->create();
        $automation = Automation::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($owner)
            ->getJson(route('automations.show', $automation))
            ->assertOk()
            ->assertJsonPath('data.id', $automation->id);

        $this->actingAs($owner)
            ->putJson(route('automations.update', $automation), ['name' => 'Updated'])
            ->assertOk()
            ->assertJsonPath('data.name', 'Updated');

        $this->actingAs($owner)
            ->deleteJson(route('automations.destroy', $automation))
            ->assertNoContent();

        $this->assertDatabaseMissing('automations', ['id' => $automation->id]);
    }

    public function test_user_cannot_view_update_or_delete_foreign_automation(): void
    {
        [$owner, $stranger] = User::factory()->count(2)->create();
        $automation = Automation::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($stranger)
            ->getJson(route('automations.show', $automation))
            ->assertForbidden();

        $this->actingAs($stranger)
            ->putJson(route('automations.update', $automation), ['name' => 'Hijacked'])
            ->assertForbidden();

        $this->actingAs($stranger)
            ->deleteJson(route('automations.destroy', $automation))
            ->assertForbidden();

        $this->assertDatabaseHas('automations', [
            'id' => $automation->id,
            'name' => $automation->name,
        ]);
    }

    public function test_user_can_list_only_own_connections_without_tokens(): void
    {
        [$owner, $stranger] = User::factory()->count(2)->create();
        $own = ServiceConnection::factory()->create([
            'user_id' => $owner->id,
            'provider' => 'github',
            'access_token' => 'secret-access',
            'refresh_token' => 'secret-refresh',
        ]);
        ServiceConnection::factory()->create(['user_id' => $stranger->id]);

        $response = $this->actingAs($owner)->getJson(route('connections.index'));

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.id', $own->id);
        $response->assertJsonMissingPath('data.0.access_token');
        $response->assertJsonMissingPath('data.0.refresh_token');
    }

    public function test_user_cannot_view_update_or_delete_foreign_connection(): void
    {
        [$owner, $stranger] = User::factory()->count(2)->create();
        $connection = ServiceConnection::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($stranger)
            ->getJson(route('connections.show', $connection))
            ->assertForbidden();

        $this->actingAs($stranger)
            ->putJson(route('connections.update', $connection), ['status' => 'revoked'])
            ->assertForbidden();

        $this->actingAs($stranger)
            ->deleteJson(route('connections.destroy', $connection))
            ->assertForbidden();

        $this->assertDatabaseHas('service_connections', ['id' => $connection->id]);
    }

    public function test_user_can_view_update_and_delete_own_connection(): void
    {
        $owner = User::factory()->create();
        $connection = ServiceConnection::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($owner)
            ->getJson(route('connections.show', $connection))
            ->assertOk()
            ->assertJsonMissingPath('data.access_token')
            ->assertJsonMissingPath('data.refresh_token');

        $this->actingAs($owner)
            ->putJson(route('connections.update', $connection), ['status' => 'revoked'])
            ->assertOk();

        $this->actingAs($owner)
            ->deleteJson(route('connections.destroy', $connection))
            ->assertNoContent();

        $this->assertDatabaseMissing('service_connections', ['id' => $connection->id]);
    }

    public function test_user_can_list_only_own_executions(): void
    {
        [$owner, $stranger] = User::factory()->count(2)->create();
        $ownAutomation = Automation::factory()->create(['user_id' => $owner->id]);
        $foreignAutomation = Automation::factory()->create(['user_id' => $stranger->id]);

        $own = AutomationExecution::factory()->create([
            'user_id' => $owner->id,
            'automation_id' => $ownAutomation->id,
        ]);
        AutomationExecution::factory()->create([
            'user_id' => $stranger->id,
            'automation_id' => $foreignAutomation->id,
        ]);

        $response = $this->actingAs($owner)->getJson(route('executions.index'));

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.id', $own->id);
    }

    public function test_user_cannot_view_update_or_delete_foreign_execution(): void
    {
        [$owner, $stranger] = User::factory()->count(2)->create();
        $automation = Automation::factory()->create(['user_id' => $owner->id]);
        $execution = AutomationExecution::factory()->create([
            'user_id' => $owner->id,
            'automation_id' => $automation->id,
        ]);

        $this->actingAs($stranger)
            ->getJson(route('executions.show', $execution))
            ->assertForbidden();

        $this->actingAs($stranger)
            ->putJson(route('executions.update', $execution), ['status' => 'failed'])
            ->assertForbidden();

        $this->actingAs($stranger)
            ->deleteJson(route('executions.destroy', $execution))
            ->assertForbidden();

        $this->assertDatabaseHas('automation_executions', ['id' => $execution->id]);
    }

    public function test_user_can_view_update_and_delete_own_execution(): void
    {
        $owner = User::factory()->create();
        $automation = Automation::factory()->create(['user_id' => $owner->id]);
        $execution = AutomationExecution::factory()->create([
            'user_id' => $owner->id,
            'automation_id' => $automation->id,
        ]);

        $this->actingAs($owner)
            ->getJson(route('executions.show', $execution))
            ->assertOk()
            ->assertJsonPath('data.id', $execution->id);

        $this->actingAs($owner)
            ->putJson(route('executions.update', $execution), ['status' => 'successful'])
            ->assertOk();

        $this->actingAs($owner)
            ->deleteJson(route('executions.destroy', $execution))
            ->assertNoContent();

        $this->assertDatabaseMissing('automation_executions', ['id' => $execution->id]);
    }
}
