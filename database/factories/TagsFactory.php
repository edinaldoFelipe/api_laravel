<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tags>
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
            'tag_name'=>$this->faker->realText(20,1),
            'task_id'=>$this->faker->randomElement(\App\Models\Task::pluck('id')->toArray()),
        ];
    }
}
