<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
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
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'username' => $this->faker->unique()->userName(),
            'address' => $this->faker->address(),
            'password' => Hash::make('password'),
            'role' => 'user',
            'description' => $this->faker->paragraph(),
            'image_url' => null,
            'is_active' => true,
            'is_admin' => false,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate a specific password for the user.
     *
     * @param string $password
     * @return $this
     */
    public function withPassword(string $password): static
    {
        return $this->state(fn (array $attributes) => [
            'password' => $password,
        ]);
    }

    /**
     * Indicate that the user has a 'company' role.
     */
    public function company(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'company',
        ]);
    }

    /**
     * Indicate that the user has a 'provider' role.
     */
    public function provider(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'provider',
        ]);
    }

    /**
     * Indicate that the user is an admin.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_admin' => true,
        ]);
    }

    /**
     * Indicate that the user is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
