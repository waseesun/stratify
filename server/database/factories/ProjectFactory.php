<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Proposal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Project::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Get an accepted proposal that doesn't have a project yet
        $proposal = Proposal::where('status', 'accepted')
                            ->doesntHave('project')
                            ->inRandomOrder()
                            ->first();

        // If no suitable proposal is found, you can handle it gracefully.
        // The seeder will be structured to avoid this.
        if (!$proposal) {
            return [];
        }

        $startDate = $this->faker->dateTimeBetween('-1 year', 'now');
        $endDate = $this->faker->dateTimeBetween($startDate, '+1 year');

        return [
            'problem_id' => $proposal->problem_id,
            'proposal_id' => $proposal->id,
            'fee' => $this->faker->numberBetween(1000, 100000),
            'status' => $this->faker->randomElement(['in_progress', 'completed', 'cancelled']),
            'start_date' => $startDate,
            'end_date' => $endDate,
        ];
    }

    /**
     * Indicate that the project is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'end_date' => now(),
        ]);
    }
}