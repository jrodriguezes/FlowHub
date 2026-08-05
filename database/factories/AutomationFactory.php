<?php

namespace Database\Factories;

use App\Models\Automation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Automation>
 */
class AutomationFactory extends Factory
{
    protected $model = Automation::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->sentence(3),
            'description' => fake()->optional()->paragraph(),
            'is_active' => true,
        ];
    }
}
