<?php

namespace App\Adapters;

use App\DTO\ActionResult;

use App\Models\ServiceConnection;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;

class GitHubAdapter extends AbstractProviderAdapter
{
    public function provider(): string
    {
        return 'github';
    }

    public function supportedActions(): array
    {
        return [
            'github.create_issue',
        ];
    }
    // actionType = provider.action (example. google.send_email)
    // $parameters = the order details (example. the repo and the title and body)
    // $connection = the connection details
    protected function perform(string $actionType, array $parameters, ServiceConnection $connection): ActionResult
    {
        return match ($actionType) {
            'github.create_issue' => $this->createIssue($parameters, $connection),
            default => throw new InvalidArgumentException($actionType),
        };
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    private function createIssue(array $parameters, ServiceConnection $connection): ActionResult
    {
        [$owner, $repo] = $this->parseRepository($parameters);
        $title = trim((string) ($parameters['title'] ?? ''));
        $body = (string) ($parameters['body'] ?? $parameters['description'] ?? '');

        if ($title === '') {
            throw new \RuntimeException('El título del issue es obligatorio.');
        }

        try {
            $response = $this->githubHttp($connection)
                ->post("https://api.github.com/repos/{$owner}/{$repo}/issues", [
                    'title' => $title,
                    'body' => $body !== '' ? $body : null,
                ]);
        } catch (ConnectionException $exception) {
            throw new \RuntimeException('GitHub no respondió a tiempo.');
        }

        if ($response->successful()) {
            $payload = $response->json() ?? [];

            return ActionResult::ok(
                provider: $this->provider(),
                externalId: isset($payload['id']) ? (string) $payload['id'] : null,
                data: [
                    'issue_id' => $payload['id'] ?? null,
                    'number' => $payload['number'] ?? null,
                    'url' => $payload['html_url'] ?? null,
                    'title' => $payload['title'] ?? $title,
                ],
            );
        }

        throw $this->classifyError($response);
    }

    /**
     * @param  array<string, mixed>  $parameters
     * @return array{0: string, 1: string}
     */
    private function parseRepository(array $parameters): array
    {
        $repository = trim((string) ($parameters['repository'] ?? $parameters['repo'] ?? ''));

        if (!preg_match('/^([A-Za-z0-9_.-]+)\/([A-Za-z0-9_.-]+)$/', $repository, $matches)) {
            throw new \RuntimeException('El repositorio debe tener el formato owner/repo.');
        }

        return [$matches[1], $matches[2]];
    }

    private function githubHttp(ServiceConnection $connection): PendingRequest
    {
        $verify = config('services.github.guzzle.verify', true);

        return Http::withToken((string) $connection->access_token)
            ->accept('application/vnd.github+json')
            ->withHeaders([
                'X-GitHub-Api-Version' => '2022-11-28',
                'User-Agent' => 'FlowHub',
            ])
            ->timeout(15)
            ->withOptions(['verify' => $verify]);
    }

    private function classifyError(Response $response): \RuntimeException
    {
        $status = $response->status();
        $retryAfter = $response->header('Retry-After');
        $message = $this->sanitizeGitHubMessage($response->json('message') ?? $response->body());

        $retryable = in_array($status, [408, 429, 500, 502, 503, 504], true);

        $friendly = match (true) {
            $status === 401, $status === 403 => 'GitHub rechazó el token o los permisos de la conexión.',
            $status === 404 => 'No se encontró el repositorio o no hay acceso.',
            $status === 422 => 'GitHub rechazó el issue: ' . $message,
            $status === 429 => 'GitHub alcanzó el límite de tasa.',
            $retryable => 'GitHub no está disponible temporalmente.',
            default => 'GitHub devolvió un error (HTTP ' . $status . ').',
        };

        return new \RuntimeException($friendly);
    }

    private function sanitizeGitHubMessage(string $message): string
    {
        $message = preg_replace('/ghp_[A-Za-z0-9_]+/', '[redacted]', $message) ?? $message;
        $message = preg_replace('/gho_[A-Za-z0-9_]+/', '[redacted]', $message) ?? $message;

        return mb_substr(trim($message), 0, 300);
    }
}
