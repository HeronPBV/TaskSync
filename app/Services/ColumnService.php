<?php

namespace App\Services;

use App\Exceptions\ServiceException;
use App\Models\Board;
use App\Models\Column;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class ColumnService
{
    protected $cacheTTL = 3600; // 1 hour

    /**
     * Retrieve all columns for a given board, caching the result.
     *
     * @param Board $board
     * @return Collection
     */
    public function getColumnsForBoard(Board $board): Collection
    {
        $cacheKey = "board:{$board->id}:columns";
        return Cache::remember($cacheKey, $this->cacheTTL, function () use ($board) {
            return $board->columns()->with('tasks')->get(); // Ensure eager loading
        });
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
        $data['board_id'] = $board->id;
        $column = Column::create($data);
        if (!$column) {
            throw new ServiceException("Column creation failed.");
        }

        $this->invalidateColumnCache($board);
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

        $this->invalidateColumnCache($column->board);
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

        $this->invalidateColumnCache($column->board);
        return $result;
    }

    /**
     * Invalidate cache keys related to a board's columns.
     *
     * @param Board $board
     */
    protected function invalidateColumnCache(Board $board): void
    {
        Cache::forget("board:{$board->id}:columns");
        Cache::forget("board:{$board->id}:details");
    }
}
