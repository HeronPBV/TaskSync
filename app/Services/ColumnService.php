<?php

namespace App\Services;

use App\Models\Board;
use App\Models\Column;
use Illuminate\Database\Eloquent\Collection;

class ColumnService
{
    /**
     * Retrieve all columns for a given board.
     *
     * @param Board $board
     * @return Collection
     */
    public function getColumnsForBoard(Board $board): Collection
    {
        return $board->columns()->get();
    }

    /**
     * Create a new column for the given board.
     *
     * @param Board $board
     * @param array $data
     * @return Column
     */
    public function createColumn(Board $board, array $data): Column
    {
        $lastPosition = $board->columns()->max('position');

        $data['position'] = is_null($lastPosition) ? 1 : $lastPosition + 1;

        $data['board_id'] = $board->id;

        return Column::create($data);
    }

    /**
     * Update an existing column.
     *
     * @param Column $column
     * @param array $data
     * @return bool
     */
    public function updateColumn(Column $column, array $data): bool
    {
        return $column->update($data);
    }

    /**
     * Delete (soft delete) a column.
     *
     * @param Column $column
     * @return bool|null
     */
    public function deleteColumn(Column $column): bool|null
    {
        return $column->delete();
    }
}
