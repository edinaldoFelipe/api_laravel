<?php

namespace Database\Factories\V1;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\V1\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->realText(30, 1),
            'description' => $this->faker->text,
            'status' => $this->faker->randomElement(['BACKLOG', 'IN_PROGRESS', 'WAITING_CUSTOMER_APPROVAL', 'APPROVED']),
            'file_url' => $this->faker->url,
        ];
    }
}
