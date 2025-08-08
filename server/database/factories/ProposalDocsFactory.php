<?php

namespace Database\Factories;

use App\Models\Proposal;
use App\Models\ProposalDocs;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProposalDocs>
 */
class ProposalDocsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProposalDocs::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'proposal_id' => Proposal::inRandomOrder()->first()->id,
            'file_url' => $this->faker->url() . '.pdf', // Example file URL
        ];
    }
}