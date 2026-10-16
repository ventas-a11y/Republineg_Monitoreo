<?php

namespace Database\Factories;

use App\Enums\Role;
use App\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'role' => Role::Empleado,
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the user is an Administrador.
     */
    public function administrador(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => Role::Administrador,
        ]);
    }

    /**
     * Indicate that the user is a Revisor of the given Áreas.
     */
    public function revisor(Area ...$areas): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => Role::Revisor,
        ])->afterCreating(fn (User $user) => $user->reviewedAreas()->attach(collect($areas)->pluck('id')));
    }

    /**
     * Indicate that the user is an Empleado of the given Área.
     */
    public function empleado(?Area $area = null): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => Role::Empleado,
            'area_id' => $area?->id,
        ]);
    }

    /**
     * Indicate that the user still has the temporary password sent by invitation.
     */
    public function withTemporaryPassword(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
            'password_change_required' => true,
            'temporary_password_expires_at' => now()->addHours(72),
        ]);
    }

    /**
     * Indicate that the model has two-factor authentication configured.
     */
    public function withTwoFactor(): static
    {
        return $this->state(fn (array $attributes) => [
            'two_factor_secret' => encrypt('secret'),
            'two_factor_recovery_codes' => encrypt(json_encode(['recovery-code-1'])),
            'two_factor_confirmed_at' => now(),
        ]);
    }
}
