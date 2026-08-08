<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAutomationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // automation data validation
        return [
            'name' => ["required", "string", "max:255"],
            'description' => ["nullable", "string", "max:255"],
            "is_active" => ["required", "boolean"],

            // trigger data validation
            'trigger' => ["required", "array"],
            'trigger.type' => ["required", "string", "max:255"],
            'trigger.cron_expression' => ["nullable", "string", "max:255"],
            
            // conditions data validation
            'conditions' => ["nullable", "array"],
            'conditions.*.field' => ["required", "string", "max:255"],
            'conditions.*.operator' => ["required", "string", "max:255"],
            'conditions.*.value' => ["required", "string", "max:255"],

            // actions data validation
            'actions' => ["required", "array", "min:1"],
            'actions.*.type' => ["required", "string", "max:255"],
            'actions.*.config' => ["nullable", "array"],
        ];
    }
}
