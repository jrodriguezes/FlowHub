<?php

namespace Database\Factories;

use App\Enums\ExecutionStatus;
use App\Models\Automation;
use App\Models\AutomationExecution;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AutomationExecution>
 */
class AutomationExecutionFactory extends Factory
{
    protected $model = AutomationExecution::class;

    public function definition(): array
    {
        return [
            'automation_id' => Automation::factory(),
            'user_id' => User::factory(),
            'event_key' => 'test:'.fake()->uuid(),
            'status' => ExecutionStatus::PENDING,
            'input_payload' => ['trigger' => ['issue' => ['title' => 'urgente']]],
            'output_payload' => null,
            'error_details' => null,
        ];
    }

    public function forOwner(User $user, ?Automation $automation = null): static
    {
        return $this->state(function () use ($user, $automation) {
            $automation ??= Automation::factory()->create(['user_id' => $user->id]);

            return [
                'user_id' => $user->id,
                'automation_id' => $automation->id,
            ];
        });
    }

    public function successful(): static
    {
        return $this->state(fn () => [
            'status' => ExecutionStatus::SUCCESSFUL,
            'started_at' => now()->subMinutes(2),
            'completed_at' => now()->subMinute(),
            'output_payload' => ['result' => 'ok'],
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn () => [
            'status' => ExecutionStatus::FAILED,
            'started_at' => now()->subMinutes(2),
            'completed_at' => now()->subMinute(),
            'error_details' => ['message' => 'Error simulado'],
        ]);
    }

    public function skipped(): static
    {
        return $this->state(fn () => [
            'status' => ExecutionStatus::SKIPPED,
            'completed_at' => now(),
            'error_details' => ['message' => 'Condiciones no cumplidas'],
        ]);
    }
}
