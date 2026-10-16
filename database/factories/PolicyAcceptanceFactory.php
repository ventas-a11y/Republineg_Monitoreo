<?php

namespace Database\Factories;

use App\Models\MonitoringPolicy;
use App\Models\PolicyAcceptance;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PolicyAcceptance>
 */
class PolicyAcceptanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'monitoring_policy_id' => MonitoringPolicy::factory(),
            'accepted_at' => now(),
            'ip_address' => fake()->ipv4(),
            'user_agent' => 'Mozilla/5.0',
        ];
    }
}
