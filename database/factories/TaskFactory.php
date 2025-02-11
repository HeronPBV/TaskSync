<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\Column;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition()
    {
        return [
            'column_id' => Column::factory(),
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'due_date' => $this->faker->optional()->dateTimeBetween('now', '+1 month'),
            'priority' => $this->faker->randomElement(['high', 'medium', 'low']),
            'position' => $this->faker->numberBetween(1, 10),
        ];
    }
}

