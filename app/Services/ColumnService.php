<?php

namespace App\Services;

use App\Exceptions\ServiceException;
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
     * @throws ServiceException
     */
    public function createColumn(Board $board, array $data): Column
    {
        $lastPosition = $board->columns()->max('position');
        $data['position'] = is_null($lastPosition) ? 1 : $lastPosition + 1;
        $data['board_id'] = $board->id;
        $column = Column::create($data);
        if (!$column) {
            throw new ServiceException("Column creation failed.");
        }
        return $column;
    }

    /**
     * Update an existing column.
     *
     * @param Column $column
     * @param array $data
     * @return bool
     * @throws ServiceException
     */
    public function updateColumn(Column $column, array $data): bool
    {
        $result = $column->update($data);
        if (!$result) {
            throw new ServiceException("Column update failed.");
        }
        return $result;
    }

    /**
     * Delete (soft delete) a column.
     *
     * @param Column $column
     * @return bool|null
     * @throws ServiceException
     */
    public function deleteColumn(Column $column): ?bool
    {
        $result = $column->delete();
        if (!$result) {
            throw new ServiceException("Column deletion failed.");
        }
        return $result;
    }
}
