<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Review;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Review::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $reviewer = User::inRandomOrder()->first();
        $reviewee = User::where('id', '!=', $reviewer->id)->inRandomOrder()->first();

        // Ensure both reviewer and reviewee exist before creating a review
        if (!$reviewer || !$reviewee) {
            return [];
        }

        return [
            'reviewer_id' => $reviewer->id,
            'reviewee_id' => $reviewee->id,
            'rating' => $this->faker->numberBetween(1, 5),
            'comment' => $this->faker->paragraph(),
        ];
    }
}