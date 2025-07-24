<?php

namespace Database\Factories;

use App\Models\ProblemSkillset;
use App\Models\Problem;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProblemSkillsetFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProblemSkillset::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Ensure a problem exists or create one using its factory
        $problem = Problem::factory()->create();

        return [
            'problem_id' => $problem->id,
            'skill' => $this->faker->randomElement([
                'Web Development', 'Mobile App Development', 'UI/UX Design',
                'Backend Development', 'Frontend Development', 'Database Management',
                'Cloud Computing', 'Data Analysis', 'Machine Learning', 'Cybersecurity'
            ]),
        ];
    }
}
