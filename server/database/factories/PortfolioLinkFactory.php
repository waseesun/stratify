<?php

namespace Database\Factories;

use App\Models\PortfolioLink;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PortfolioLinkFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PortfolioLink::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Ensure a provider user exists or create one
        $providerUser = User::firstOrCreate(
            ['email' => 'provider@example.com', 'role' => 'provider'],
            [
                'first_name' => $this->faker->firstName,
                'last_name' => $this->faker->lastName,
                'username' => $this->faker->unique()->userName,
                'address' => $this->faker->address,
                'password' => bcrypt('password'), // default password
                'description' => $this->faker->paragraph,
                'image_url' => $this->faker->imageUrl(),
                'is_active' => true,
                'is_admin' => false,
                'is_super_admin' => false,
            ]
        );
        // If the user already exists but doesn't have the 'provider' role, update it
        if ($providerUser->role !== 'provider') {
            $providerUser->update(['role' => 'provider']);
        }

        return [
            'provider_id' => $providerUser->id,
            'link' => $this->faker->url,
        ];
    }
}
