<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Problem;
use App\Models\Proposal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Proposal>
 */
class ProposalFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Proposal::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Get a random provider and problem from the database
        $provider = User::where('role', 'provider')->inRandomOrder()->first();
        $problem = Problem::inRandomOrder()->first();

        // Ensure both provider and problem exist before creating a proposal
        if (!$provider || !$problem) {
            // Handle this case as needed, e.g., throw an exception or return an empty array
            // For a seeder, you'll need to make sure users and problems are created first.
            // Our seeder will handle this order.
            return [];
        }

        return [
            'provider_id' => $provider->id,
            'problem_id' => $problem->id,
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(['submitted', 'accepted', 'rejected']),
        ];
    }
}