<?php

namespace Tests\Unit;

use App\Adapters\GitHubAdapter;
use App\Adapters\GoogleAdapter;
use App\Enums\ConnectionStatus;

use App\Models\ServiceConnection;
use App\Models\User;
use App\Services\ProviderManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Fakes\FakeSlackAdapter;
use Tests\TestCase;

class ProviderManagerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_resolves_github_and_google_adapters(): void
    {
        $manager = app(ProviderManager::class);

        $this->assertInstanceOf(GitHubAdapter::class, $manager->adapterFor('github'));
        $this->assertInstanceOf(GoogleAdapter::class, $manager->adapterFor('google'));
        $this->assertSame(['github', 'google'], $manager->registeredProviders());
    }

    public function test_unknown_provider_throws_a_domain_exception(): void
    {
        $this->expectException(\RuntimeException::class);

        app(ProviderManager::class)->adapterFor('discord');
    }

    public function test_github_adapter_rejects_unsupported_actions(): void
    {
        $connection = $this->connectionFor('github');

        $this->expectException(\RuntimeException::class);

        app(ProviderManager::class)->execute(
            'github',
            'google.send_email',
            [],
            $connection,
        );
    }

    public function test_google_adapter_supports_email_and_calendar_simulations(): void
    {
        $connection = $this->connectionFor('google');
        $manager = app(ProviderManager::class);

        $email = $manager->execute('google', 'google.send_email', [
            'to' => 'ops@example.com',
            'subject' => 'Alerta',
        ], $connection);

        $event = $manager->execute('google', 'google.create_calendar_event', [
            'title' => 'Sync',
        ], $connection);

        $this->assertTrue($email->success);
        $this->assertSame('fake-gmail-message-1', $email->externalId);
        $this->assertTrue($event->success);
        $this->assertSame('fake-calendar-event-1', $event->externalId);
    }

    public function test_revoked_connection_is_rejected(): void
    {
        $connection = $this->connectionFor('github', ConnectionStatus::REVOKED);

        $this->expectException(\RuntimeException::class);

        app(ProviderManager::class)->execute(
            'github',
            'github.create_issue',
            ['title' => 'No debe ejecutarse'],
            $connection,
        );
    }

    public function test_a_new_adapter_can_be_registered_without_changing_the_manager(): void
    {
        $manager = app(ProviderManager::class);
        $manager->extend('slack', FakeSlackAdapter::class);

        $connection = ServiceConnection::factory()->create([
            'user_id' => User::factory(),
            'provider' => 'slack',
            'status' => ConnectionStatus::ACTIVE,
        ]);

        $result = $manager->execute('slack', 'slack.post_message', [
            'channel' => '#alerts',
        ], $connection);

        $this->assertInstanceOf(FakeSlackAdapter::class, $manager->adapterFor('slack'));
        $this->assertTrue($result->success);
        $this->assertSame('slack', $result->provider);
        $this->assertSame('#alerts', $result->data['channel']);
    }

    private function connectionFor(string $provider, ConnectionStatus $status = ConnectionStatus::ACTIVE): ServiceConnection
    {
        return ServiceConnection::factory()->create([
            'user_id' => User::factory(),
            'provider' => $provider,
            'status' => $status,
            'revoked_at' => $status === ConnectionStatus::REVOKED ? now() : null,
        ]);
    }
}
