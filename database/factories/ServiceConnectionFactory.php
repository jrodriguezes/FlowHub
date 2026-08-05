<?php

namespace Database\Factories;

use App\Enums\ConnectionStatus;
use App\Models\ServiceConnection;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServiceConnection>
 */
class ServiceConnectionFactory extends Factory
{
    protected $model = ServiceConnection::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'provider' => fake()->randomElement(['github', 'google']),
            'external_id' => (string) fake()->unique()->numerify('########'),
            'access_token' => 'test-access-token',
            'refresh_token' => 'test-refresh-token',
            'scopes' => ['read:user'],
            'expires_at' => now()->addHour(),
            'status' => ConnectionStatus::ACTIVE,
            'revoked_at' => null,
        ];
    }
}
