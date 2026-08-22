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

    // actionType = provider.action (example. google.send_email)
    // $parameters = the order details (example. where the email go to and the content)
    // $connection = the connection details
    protected function perform(string $actionType, array $parameters, ServiceConnection $connection): ActionResult
    {
        // M09 reemplazara estas respuestas simuladas por Gmail API y Calendar API.
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
