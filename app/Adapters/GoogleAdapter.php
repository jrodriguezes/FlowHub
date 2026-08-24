<?php

namespace App\Adapters;

use App\DTO\ActionResult;
use App\Models\ServiceConnection;
use \Illuminate\Support\Facades\Http;
use \Illuminate\Http\Client\Response;
use \Illuminate\Support\Facades\Log;
use \App\Services\TokenRefreshService;
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
        return match ($actionType) {
            'google.send_email' => $this->sendEmail($parameters, $connection),
            'google.create_calendar_event' => $this->createCalendarEvent($parameters, $connection),
            default => throw new \InvalidArgumentException($actionType),
        };
    }

    private function sendEmail(array $parameters, ServiceConnection $connection): ActionResult
    {
        $to = trim((string) ($parameters['to'] ?? ''));
        $subject = trim((string) ($parameters['subject'] ?? ''));
        $body = trim((string) ($parameters['body'] ?? ''));

        if ($to === '' || $subject === '' || $body === '') {
            return ActionResult::failure($this->provider(), 'Faltan parámetros obligatorios');
        }

        // we build the message with the standart format of the emails
        $message = "To: {$to}\r\n";
        $message .= "Subject: =?utf-8?B?" . base64_encode($subject) . "?=\r\n";
        $message .= "Content-Type: text/plain; charset=utf-8\r\n\r\n";
        $message .= $body;

        // gmail api requires base64 without signs 
        $base64url = rtrim(strtr(base64_encode($message), '+/', '-_'), '=');

        // we send the mail, we have a custom response from the API, we extract what we need 
        $response = $this->googleHttp($connection)->post('https://gmail.googleapis.com/gmail/v1/users/me/messages/send', ['raw' => $base64url]);

        if ($response->successful()) {
            $payload = $response->json(); // we want the payload in json format

            // and return a template response. we do this because our core will receive a generic information for all oauths
            return ActionResult::ok(
                provider: $this->provider(),
                externalId: $payload['id'] ?? null,
                data: [
                    'action' => 'google.send_email',
                    'message_id' => $payload['id'] ?? null,
                    'thread_id' => $payload['threadId'] ?? null,
                ]
            );
        }

        throw $this->classifyError($response);
    }

    // we prepare our mail carrier 
    private function googleHttp(ServiceConnection $connection)
    {
        // we use our new services with redis locks
        $tokenService = app(TokenRefreshService::class);
        $tokenService->refreshTokenIfNeeded($connection);

        // we refresh the connection just in case the service updated the token
        $connection->refresh();

        // we deactivate the ssl certification in case of an issue with the domain of google
        $verify = config('services.google.guzzle.verify', true);

        // Http. we use the laravel http, when we pass the token the mail carrier says 'paste the bearer token here'
        return Http::withToken((string) $connection->access_token)
            ->acceptJson() // we want json response
            ->timeout(15) // wait 15 secs for a response
            ->withOptions(['verify' => $verify]); // we deactive the ssl cerification in case of an issue with the domain of google
    }

    private function createCalendarEvent(array $parameters, ServiceConnection $connection): ActionResult
    {
        $summary = trim((string) ($parameters['summary'] ?? ''));
        $start = $parameters['start'] ?? null;
        $end = $parameters['end'] ?? null;
        $timezone = $parameters['timezone'] ?? 'UTC';

        $payload = [
            'summary' => $summary, // event title
            'start' => [ // when the calendar start date, and timezone
                'dateTime' => \Carbon\Carbon::parse($start, $timezone)->toIso8601String(), // correctly apply timezone offset
                'timeZone' => $timezone,
            ],
            'end' => [ // when the calendar end, date and timezone
                'dateTime' => \Carbon\Carbon::parse($end, $timezone)->toIso8601String(), // correctly apply timezone offset
                'timeZone' => $timezone,
            ],
        ];

        // the will use the principal calendary 
        $response = $this->googleHttp($connection)->post('https://www.googleapis.com/calendar/v3/calendars/primary/events', $payload);

        if ($response->successful()) {
            $data = $response->json();
            return ActionResult::ok(
                provider: $this->provider(),
                externalId: $data['id'] ?? null,
                data: [
                    'action' => 'google.create_calendar_event',
                    'event_id' => $data['id'] ?? null,
                    'url' => $data['htmlLink'] ?? null,
                ]
            );
        }

        throw $this->classifyError($response);
    }

    private function classifyError(Response $response): \RuntimeException
    {
        $status = $response->status();

        // detect 429 and we put the seconds of wait in the second parameter (error code)
        if ($status === 429) {
            $retryAfter = (int) $response->header('Retry-After', 60);
            return new \RuntimeException('RATE_LIMIT_EXCEEDED', $retryAfter);
        }

        $retryable = in_array($status, [408, 500, 502, 503, 504], true);

        $friendly = match (true) {
            $status === 401 || $status === 403 => 'Google rechazó el token o faltan permisos (scopes).',
            $status === 404 => 'El recurso solicitado en Google no existe.',
            $retryable => 'Los servidores de Google no están disponibles temporalmente.',
            default => 'Google devolvió un error inesperado (HTTP ' . $status . ').',
        };

        return new \RuntimeException($friendly);
    }


}
