<?php

namespace App\Adapters;

use App\DTO\ActionResult;
use App\Models\ServiceConnection;

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

    protected function perform(string $actionType, array $parameters, ServiceConnection $connection): ActionResult
    {
        // M08 reemplazará esta respuesta simulada por la GitHub REST API.
        $title = (string) ($parameters['title'] ?? 'untitled');

        return ActionResult::ok(
            provider: $this->provider(),
            externalId: 'fake-github-issue-1',
            data: [
                'simulated' => true,
                'action' => $actionType,
                'number' => 1,
                'title' => $title,
                'url' => 'https://github.com/example/repo/issues/1',
            ],
        );
    }
}
