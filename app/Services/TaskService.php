<?php

namespace App\Services;

use App\Models\Task;
use App\Models\Column;
use Illuminate\Database\Eloquent\Collection;

class TaskService
{
    /**
     * Retrieve all tasks for a given column.
     *
     * @param Column $column
     * @return Collection
     */
    public function getTasksForColumn(Column $column): Collection
    {
        return $column->tasks()->get();
    }

    /**
     * Create a new task under a specific column.
     *
     * @param Column $column
     * @param array $data
     * @return Task
     */
    public function createTask(Column $column, array $data): Task
    {
        return $column->tasks()->create($data);
    }

    /**
     * Update an existing task.
     *
     * @param Task $task
     * @param array $data
     * @return bool
     */
    public function updateTask(Task $task, array $data): bool
    {
        return $task->update($data);
    }

    /**
     * Delete (soft delete) a task.
     *
     * @param Task $task
     * @return bool|null
     */
    public function deleteTask(Task $task): bool|null
    {
        return $task->delete();
    }
}
