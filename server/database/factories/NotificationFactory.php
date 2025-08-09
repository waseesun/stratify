<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notification>
 */
class NotificationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Notification::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ['info', 'problem', 'proposal', 'project', 'transaction', 'review'];
        $type = $this->faker->randomElement($types);

        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'message' => $this->faker->sentence(),
            'type' => $type,
            'link' => $this->faker->url(),
            'is_read' => $this->faker->boolean(),
        ];
    }
}