<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $project = Project::with(['problem.company', 'proposal.provider'])->inRandomOrder()->first();

        // Ensure a valid project with relationships exists before creating a transaction
        if (!$project || !$project->proposal || !$project->problem) {
            return [];
        }

        return [
            'project_id' => $project->id,
            'provider_id' => $project->proposal->provider->id,
            'company_id' => $project->problem->company->id,
            'milestone_name' => 'Milestone ' . $this->faker->numberBetween(1, 5),
            'amount' => $this->faker->numberBetween(100, 5000),
            'release_date' => $this->faker->dateTimeBetween($project->start_date, $project->end_date),
        ];
    }
}