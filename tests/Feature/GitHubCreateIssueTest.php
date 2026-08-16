<?php

namespace Tests\Feature;

use App\Enums\ConnectionStatus;
use App\Exceptions\ProviderRequestException;
use App\Models\ServiceConnection;
use App\Models\User;
use App\Services\ProviderManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class GitHubCreateIssueTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_an_issue_and_returns_ids(): void
    {
        Http::fake([
            'https://api.github.com/repos/octocat/Hello-World/issues' => Http::response([
                'id' => 4242,
                'number' => 17,
                'html_url' => 'https://github.com/octocat/Hello-World/issues/17',
                'title' => 'Falla el login',
            ], 201),
        ]);

        $result = app(ProviderManager::class)->execute(
            'github',
            'github.create_issue',
            [
                'repository' => 'octocat/Hello-World',
                'title' => 'Falla el login',
                'body' => 'Pasos para reproducir',
            ],
            $this->githubConnection(),
        );

        $this->assertTrue($result->success);
        $this->assertSame('4242', $result->externalId);
        $this->assertSame(17, $result->data['number']);
        $this->assertSame('https://github.com/octocat/Hello-World/issues/17', $result->data['url']);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.github.com/repos/octocat/Hello-World/issues'
                && $request->hasHeader('Authorization', 'Bearer github-access-token')
                && $request->hasHeader('X-GitHub-Api-Version', '2022-11-28')
                && $request['title'] === 'Falla el login';
        });
    }

    #[DataProvider('permanentFailureProvider')]
    public function test_permanent_github_errors_are_not_retryable(int $status): void
    {
        Http::fake([
            'https://api.github.com/repos/octocat/Hello-World/issues' => Http::response(['message' => 'nope'], $status),
        ]);

        try {
            app(ProviderManager::class)->execute(
                'github',
                'github.create_issue',
                ['repository' => 'octocat/Hello-World', 'title' => 'Issue'],
                $this->githubConnection(),
            );
            $this->fail('Expected ProviderRequestException');
        } catch (ProviderRequestException $exception) {
            $this->assertFalse($exception->retryable);
            $this->assertSame($status, $exception->statusCode);
            $this->assertStringNotContainsString('github-access-token', $exception->getMessage());
        }
    }

    public static function permanentFailureProvider(): array
    {
        return [
            'unauthorized' => [401],
            'forbidden' => [403],
            'not found' => [404],
            'unprocessable' => [422],
        ];
    }

    #[DataProvider('transientFailureProvider')]
    public function test_transient_github_errors_are_retryable(int $status): void
    {
        Http::fake([
            'https://api.github.com/repos/octocat/Hello-World/issues' => Http::response(['message' => 'slow down'], $status, [
                'Retry-After' => '30',
            ]),
        ]);

        try {
            app(ProviderManager::class)->execute(
                'github',
                'github.create_issue',
                ['repository' => 'octocat/Hello-World', 'title' => 'Issue'],
                $this->githubConnection(),
            );
            $this->fail('Expected ProviderRequestException');
        } catch (ProviderRequestException $exception) {
            $this->assertTrue($exception->retryable);
            $this->assertSame($status, $exception->statusCode);
            $this->assertSame(30, $exception->retryAfterSeconds);
        }
    }

    public static function transientFailureProvider(): array
    {
        return [
            'rate limit' => [429],
            'server error' => [500],
            'bad gateway' => [502],
            'unavailable' => [503],
        ];
    }

    public function test_invalid_repository_is_a_permanent_error(): void
    {
        Http::fake();

        try {
            app(ProviderManager::class)->execute(
                'github',
                'github.create_issue',
                ['repository' => 'not-a-repo', 'title' => 'Issue'],
                $this->githubConnection(),
            );
            $this->fail('Expected ProviderRequestException');
        } catch (ProviderRequestException $exception) {
            $this->assertFalse($exception->retryable);
            $this->assertSame(422, $exception->statusCode);
        }

        Http::assertNothingSent();
    }

    public function test_store_rejects_github_action_without_an_owned_github_connection(): void
    {
        $user = User::factory()->create();
        $stranger = User::factory()->create();
        $foreign = ServiceConnection::factory()->create([
            'user_id' => $stranger->id,
            'provider' => 'github',
            'status' => ConnectionStatus::ACTIVE,
        ]);

        $this->actingAs($user)
            ->from(route('automations.index'))
            ->post(route('automations.store'), [
                'name' => 'Crear issue',
                'is_active' => '1',
                'trigger' => ['type' => 'github_issue'],
                'actions' => [[
                    'type' => 'github.create_issue',
                    'service_connection_id' => $foreign->id,
                    'config' => [
                        'repository' => 'octocat/Hello-World',
                        'title' => 'Hola',
                    ],
                ]],
            ])
            ->assertRedirect(route('automations.index'))
            ->assertSessionHasErrors('actions.0.service_connection_id');
    }

    private function githubConnection(): ServiceConnection
    {
        return ServiceConnection::factory()->create([
            'user_id' => User::factory(),
            'provider' => 'github',
            'access_token' => 'github-access-token',
            'status' => ConnectionStatus::ACTIVE,
        ]);
    }
}
