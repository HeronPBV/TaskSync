<?php

namespace Database\Seeders;

use App\Models\Board;
use App\Models\Column;
use App\Models\Task;
use Illuminate\Database\Seeder;

class BoardSeeder extends Seeder
{
    public function run()
    {
        Board::factory(5)->create()->each(function ($board) {

            $columns = Column::factory(3)->create([
                'board_id' => $board->id,
            ]);

            $columns->each(function ($column) {
                Task::factory(rand(3, 7))->create([
                    'column_id' => $column->id,
                ]);
            });
        });
    }
}

