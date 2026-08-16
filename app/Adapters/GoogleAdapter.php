<?php

namespace App\Adapters;

use App\DTO\ActionResult;
use App\Models\ServiceConnection;

class GoogleAdapter extends AbstractProviderAdapter
{
    public function provider(): string
    {
        return 'google';
    }

    public function supportedActions(): array
    {
        return [
            'google.send_email',
            'google.create_calendar_event',
        ];
    }

    protected function perform(string $actionType, array $parameters, ServiceConnection $connection): ActionResult
    {
        // M09 reemplazará estas respuestas simuladas por Gmail API y Calendar API.
        return match ($actionType) {
            'google.send_email' => ActionResult::ok(
                provider: $this->provider(),
                externalId: 'fake-gmail-message-1',
                data: [
                    'simulated' => true,
                    'action' => $actionType,
                    'to' => $parameters['to'] ?? null,
                    'subject' => $parameters['subject'] ?? null,
                ],
            ),
            'google.create_calendar_event' => ActionResult::ok(
                provider: $this->provider(),
                externalId: 'fake-calendar-event-1',
                data: [
                    'simulated' => true,
                    'action' => $actionType,
                    'title' => $parameters['title'] ?? $parameters['summary'] ?? null,
                    'url' => 'https://calendar.google.com/calendar/event?eid=fake',
                ],
            ),
            default => ActionResult::failure($this->provider(), 'Acción no implementada.'),
        };
    }
}
