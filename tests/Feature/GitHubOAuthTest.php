<?php

namespace Tests\Feature;

use App\Enums\ConnectionStatus;
use App\Models\ServiceConnection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Tests\TestCase;

class GitHubOAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_start_github_oauth(): void
    {
        $this->get(route('github.redirect'))->assertRedirect(route('login'));
        $this->get(route('github.callback'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_is_redirected_to_github(): void
    {
        $user = User::factory()->create();
        $provider = $this->mockGitHubProvider();
        $provider->shouldReceive('scopes')
            ->once()
            ->with(['read:user', 'public_repo'])
            ->andReturnSelf();
        $provider->shouldReceive('redirect')
            ->once()
            ->andReturn(redirect('https://github.com/login/oauth/authorize'));

        $this->actingAs($user)
            ->get(route('github.redirect'))
            ->assertRedirect('https://github.com/login/oauth/authorize');
    }

    public function test_callback_stores_encrypted_connection_for_the_owner(): void
    {
        $user = User::factory()->create();
        $provider = $this->mockGitHubProvider();
        $provider->shouldReceive('user')->once()->andReturn($this->fakeGitHubUser());

        $this->actingAs($user)
            ->get(route('github.callback'))
            ->assertRedirect(route('connections.index'))
            ->assertSessionHas('success');

        $connection = ServiceConnection::query()
            ->where('user_id', $user->id)
            ->where('provider', 'github')
            ->first();

        $this->assertNotNull($connection);
        $this->assertSame('987654', $connection->external_id);
        $this->assertSame(ConnectionStatus::ACTIVE, $connection->status);
        $this->assertSame('github-access-token', $connection->access_token);
        $this->assertNull($connection->revoked_at);

        $raw = $connection->getRawOriginal('access_token');
        $this->assertNotSame('github-access-token', $raw);
        $this->assertNotEmpty($raw);
    }

    public function test_users_cannot_revoke_another_users_github_connection(): void
    {
        [$owner, $stranger] = User::factory()->count(2)->create();
        $connection = ServiceConnection::factory()->create([
            'user_id' => $owner->id,
            'provider' => 'github',
        ]);

        $this->actingAs($stranger)
            ->delete(route('connections.destroy', $connection))
            ->assertForbidden();

        $connection->refresh();
        $this->assertSame(ConnectionStatus::ACTIVE, $connection->status);
    }

    public function test_owner_can_revoke_github_connection_without_exposing_tokens(): void
    {
        $owner = User::factory()->create();
        $connection = ServiceConnection::factory()->create([
            'user_id' => $owner->id,
            'provider' => 'github',
            'access_token' => 'secret-access',
            'refresh_token' => 'secret-refresh',
        ]);

        $this->actingAs($owner)
            ->get(route('connections.index'))
            ->assertOk()
            ->assertDontSee('secret-access', false)
            ->assertDontSee('secret-refresh', false);

        $this->actingAs($owner)
            ->from(route('connections.index'))
            ->delete(route('connections.destroy', $connection))
            ->assertRedirect(route('connections.index'));

        $connection->refresh();

        $this->assertSame(ConnectionStatus::REVOKED, $connection->status);
        $this->assertNull($connection->access_token);
        $this->assertNotNull($connection->revoked_at);
    }

    private function mockGitHubProvider()
    {
        $provider = Mockery::mock();
        Socialite::shouldReceive('driver')->with('github')->andReturn($provider);

        return $provider;
    }

    private function fakeGitHubUser(): SocialiteUser
    {
        $githubUser = Mockery::mock(SocialiteUser::class);
        $githubUser->token = 'github-access-token';
        $githubUser->refreshToken = null;
        $githubUser->expiresIn = null;
        $githubUser->approvedScopes = ['read:user', 'public_repo'];
        $githubUser->shouldReceive('getId')->andReturn('987654');

        return $githubUser;
    }
}
