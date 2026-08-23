<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ValidatesAutomationActions;
use App\Http\Requests\Concerns\ValidatesScheduledTriggers;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAutomationRequest extends FormRequest
{
    use ValidatesAutomationActions;
    use ValidatesScheduledTriggers;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'is_active' => ['required', 'boolean'],
            'trigger' => ['required', 'array'],
            'trigger.type' => ['required', 'string', 'max:255'],
            'trigger.cron_expression' => ['required_if:trigger.type,schedule', 'nullable', 'string', 'max:255'],
            'trigger.timezone' => ['required_if:trigger.type,schedule', 'nullable', 'string', 'timezone', 'max:255'],
            'conditions' => ['nullable', 'array'],
            'conditions.*.field' => ['required', 'string', 'max:255'],
            'conditions.*.operator' => ['required', 'string', 'in:equals,not_equals,contains,exists'],
            'conditions.*.value' => ['nullable', 'string', 'max:255'],
            ...$this->automationActionRules(),
        ];
    }

    public function withValidator($validator): void
    {
        $this->validateGitHubCreateIssueConnections($validator);
        $this->validateScheduledTrigger($validator);
    }
}
