<?php

namespace App\Services;


use App\Models\Task;
use App\Models\Column;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
        DB::transaction(function () use ($column) {
            $column->tasks()->increment('position');
        });
        return $column->tasks()->create($data + ['position' => 1]);
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

    /**
     * Reorder tasks within a column, forcing that the tasks
     *
     * @param  \App\Models\Column  $destinationColumn
     * @param  array  $tasksData  Array of tasks with keys: id, position, column_id.
     * @return bool
     */
    public function reorderTasks(Column $destinationColumn, array $tasksData): bool
    {
        try {
            foreach ($tasksData as $data) {
                Task::where('id', $data['id'])->update([
                    'position' => $data['position'],
                    'column_id' => $destinationColumn->id,
                ]);
            }
            return true;
        } catch (\Exception $e) {
            Log::error('Error reordering tasks: ' . $e->getMessage());
            return false;
        }
    }
}
