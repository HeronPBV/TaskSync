<?php

namespace Database\Factories;

use App\Models\Column;
use App\Models\Board;
use Illuminate\Database\Eloquent\Factories\Factory;

class ColumnFactory extends Factory
{
    protected $model = Column::class;

    public function definition()
    {
        return [

            'board_id' => Board::factory(),
            'name' => $this->faker->word,
            'position' => $this->faker->numberBetween(1, 10),

        ];
    }
}
