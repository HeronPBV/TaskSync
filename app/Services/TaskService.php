<?php

namespace App\Services;

use App\Exceptions\ServiceException;
use App\Models\Task;
use App\Models\Column;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;
use Exception;
use Illuminate\Support\Facades\Cache;

class TaskService
{
    protected $cacheTTL = 3600; // 1 hour

    /**
     * Retrieve all tasks for a given column, caching the result.
     *
     * @param Column $column
     * @return Collection
     */
    public function getTasksForColumn(Column $column): Collection
    {
        $cacheKey = "column:{$column->id}:tasks";
        return Cache::remember($cacheKey, $this->cacheTTL, function () use ($column) {
            return $column->tasks()->orderBy('position')->get();
        });
    }

    /**
     * Create a new task under a specific column.
     *
     * @param Column $column
     * @param array $data
     * @return Task
     * @throws ServiceException
     */
    public function createTask(Column $column, array $data): Task
    {
        try {
            DB::transaction(function () use ($column) {
                $column->tasks()->increment('position');
            });

            $task = $column->tasks()->create($data + ['position' => 1]);
            if (!$task) {
                throw new ServiceException("Task creation failed.");
            }

            $this->invalidateTaskCache($column);
            return $task;
        } catch (Exception $e) {
            throw new ServiceException("Task creation failed: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Update an existing task.
     *
     * @param Task $task
     * @param array $data
     * @return bool
     * @throws ServiceException
     */
    public function updateTask(Task $task, array $data): bool
    {
        $result = $task->update($data);
        if (!$result) {
            throw new ServiceException("Task update failed.");
        }

        $this->invalidateTaskCache($task->column);
        return $result;
    }

    /**
     * Delete (soft delete) a task.
     *
     * @param Task $task
     * @return bool|null
     * @throws ServiceException
     */
    public function deleteTask(Task $task): ?bool
    {
        $result = $task->delete();
        if (!$result) {
            throw new ServiceException("Task deletion failed.");
        }

        $this->invalidateTaskCache($task->column);
        return $result;
    }

    /**
     * Reorder tasks within a column and invalidate cache.
     *
     * @param Column $destinationColumn
     * @param array $tasksData Array of tasks with keys: id, position, column_id.
     * @return bool
     * @throws ServiceException
     */
    public function reorderTasks(Column $destinationColumn, array $tasksData): bool
    {
        try {
            DB::transaction(function () use ($destinationColumn, $tasksData) {
                foreach ($tasksData as $data) {
                    Task::where('id', $data['id'])->update([
                        'position' => $data['position'],
                        'column_id' => $destinationColumn->id,
                    ]);
                }
            });

            $this->invalidateTaskCache($destinationColumn);
            return true;
        } catch (Exception $e) {
            Log::error('Error reordering tasks: ' . $e->getMessage());
            throw new ServiceException("Error reordering tasks: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Invalidate cache for a column's tasks and update related board cache.
     *
     * @param Column $column
     */
    protected function invalidateTaskCache(Column $column): void
    {
        Cache::forget("column:{$column->id}:tasks");
        Cache::forget("board:{$column->board->id}:details");
        Cache::forget("board:{$column->board->id}:columns");
    }
}
