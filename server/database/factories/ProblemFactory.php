<?php

namespace Database\Factories;

use App\Models\Problem;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProblemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Problem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Ensure a company user exists or create one
        $companyUser = User::firstOrCreate(
            ['email' => 'company@example.com', 'role' => 'company'],
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
        // If the user already exists but doesn't have the 'company' role, update it
        if ($companyUser->role !== 'company') {
            $companyUser->update(['role' => 'company']);
        }


        // Ensure a category exists or create one
        $category = Category::firstOrCreate(
            ['name' => 'General Category'],
            [] // No other attributes needed for creation if name is unique
        );

        return [
            'company_id' => $companyUser->id,
            'category_id' => $category->id,
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph,
            'budget' => $this->faker->numberBetween(1000, 100000),
            'timeline_value' => $this->faker->numberBetween(1, 12),
            'timeline_unit' => $this->faker->randomElement(['day', 'week', 'month', 'year']),
            'status' => $this->faker->randomElement(['open', 'sold', 'cancelled']),
        ];
    }
}
