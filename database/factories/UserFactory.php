<?php

namespace Database\Factories;

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
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'full_name' => fake()->name(),
            'nisn' => fake()->unique()->numerify('##########'),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => 'user',
            'account_status' => 'active',
            'rejection_reason' => null,
            'approved_at' => now(),
            'approved_by' => null,
            'class' => fake()->randomElement(['X IPA 1', 'XI IPA 2', 'XII IPS 1']),
            'phone' => fake()->phoneNumber(),
            'avatar' => null,
            'is_active' => true,
            'remember_token' => Str::random(10),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => 'admin',
            'account_status' => 'active',
            'approved_at' => now(),
            'is_active' => true,
        ]);
    }

    public function member(): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => 'user',
            'account_status' => 'active',
            'approved_at' => now(),
            'is_active' => true,
        ]);
    }

    public function pendingApproval(): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => 'user',
            'account_status' => 'pending',
            'approved_at' => null,
            'is_active' => true,
        ]);
    }

    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
