<?php

namespace App\Http\Requests\Concerns;

use App\Enums\ConnectionStatus;
use App\Models\ServiceConnection;

trait ValidatesAutomationActions
{
    /**
     * @return array<string, mixed>
     */
    protected function automationActionRules(): array
    {
        return [
            'actions' => ['required', 'array', 'min:1'],
            'actions.*.type' => ['required', 'string', 'max:255'],
            'actions.*.service_connection_id' => ['nullable', 'integer', 'exists:service_connections,id'],
            'actions.*.config' => ['nullable', 'array'],
            'actions.*.config.repository' => ['required_if:actions.*.type,github.create_issue', 'nullable', 'string', 'max:255', 'regex:/^[A-Za-z0-9_.-]+\/[A-Za-z0-9_.-]+$/'],
            'actions.*.config.title' => ['required_if:actions.*.type,github.create_issue', 'nullable', 'string', 'max:255'],
            'actions.*.config.body' => ['nullable', 'string'],
            'actions.*.config.description' => ['nullable', 'string'],

            // gmail validations
            'actions.*.config.to' => ['required_if:actions.*.type,google.send_email', 'nullable', 'email', 'max:255'],
            'actions.*.config.subject' => ['required_if:actions.*.type,google.send_email', 'nullable', 'string', 'max:255'],

            // calendar validations
            'actions.*.config.summary' => ['required_if:actions.*.type,google.create_calendar_event', 'nullable', 'string', 'max:255'],
            'actions.*.config.start' => ['required_if:actions.*.type,google.create_calendar_event', 'nullable', 'date'],
            'actions.*.config.end' => ['required_if:actions.*.type,google.create_calendar_event', 'nullable', 'date', 'after:actions.*.config.start'],
            'actions.*.config.timezone' => ['nullable', 'string', 'timezone'],

        ];

    }

    protected function validateGitHubCreateIssueConnections($validator): void
    {
        $validator->after(function ($validator) {
            foreach ($this->input('actions', []) as $index => $action) {
                if (($action['type'] ?? null) !== 'github.create_issue') {
                    continue;
                }

                $connectionId = $action['service_connection_id'] ?? null;

                if (!$connectionId) {
                    $validator->errors()->add(
                        "actions.{$index}.service_connection_id",
                        'La acción de GitHub requiere una conexión activa.',
                    );

                    continue;
                }

                $connection = ServiceConnection::query()
                    ->where('id', $connectionId)
                    ->where('user_id', $this->user()->id)
                    ->first();

                if (
                    !$connection
                    || $connection->provider !== 'github'
                    || $connection->status !== ConnectionStatus::ACTIVE
                ) {
                    $validator->errors()->add(
                        "actions.{$index}.service_connection_id",
                        'Debes usar una conexión de GitHub activa de tu cuenta.',
                    );
                }
            }
        });
    }
}
