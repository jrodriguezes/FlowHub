<?php

namespace Tests\Fakes;

use App\Adapters\AbstractProviderAdapter;
use App\DTO\ActionResult;
use App\Models\ServiceConnection;

class FakeSlackAdapter extends AbstractProviderAdapter
{
    public function provider(): string
    {
        return 'slack';
    }

    public function supportedActions(): array
    {
        return ['slack.post_message'];
    }

    protected function perform(string $actionType, array $parameters, ServiceConnection $connection): ActionResult
    {
        return ActionResult::ok(
            provider: $this->provider(),
            externalId: 'fake-slack-message-1',
            data: [
                'simulated' => true,
                'channel' => $parameters['channel'] ?? '#general',
            ],
        );
    }
}
