<?php

namespace Database\Factories\V1;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\V1\Tags>
 */
class TagsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'tag_name' => $this->faker->randomElement(['warning', 'todo', 'bug', 'urgent']),
            'task_id' => $this->faker->randomElement(\App\Models\V1\Task::pluck('id')->toArray()),
        ];
    }
}
