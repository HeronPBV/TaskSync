<?php

use App\Models\Board;
use App\Models\Column;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class)->in(__DIR__);

/*
|--------------------------------------------------------------------------
| TaskService Unit Tests
|--------------------------------------------------------------------------
|
| These tests validate the task operations:
| - Retrieving tasks for a column.
| - Creating a new task (including incrementing positions of existing tasks).
| - Updating a task.
| - Deleting a task.
| - Reordering tasks within a column.
|
*/

it('retrieves tasks for a given column', function () {
    $board = Board::factory()->create();
    $column = Column::factory()->create(['board_id' => $board->id]);
    Task::factory()->count(3)->create(['column_id' => $column->id]);

    $service = new TaskService();
    $tasks = $service->getTasksForColumn($column);

    expect($tasks->count())->toBe(3);
});

it('creates a new task under a specific column and increments existing tasks positions', function () {
    $board = Board::factory()->create();
    $column = Column::factory()->create(['board_id' => $board->id]);

    $existingTask = Task::factory()->create(['column_id' => $column->id, 'position' => 1]);

    $service = new TaskService();
    $data = [
        'title' => 'New Task',
        'description' => 'Task description',
        'due_date' => now()->toDateString(),
        'priority' => 'high',
    ];

    $newTask = $service->createTask($column, $data);

    expect($newTask->title)->toBe('New Task');
    expect($newTask->position)->toBe(1);

    $existingTask->refresh();
    expect($existingTask->position)->toBe(2);
});

it('updates an existing task', function () {
    $board = Board::factory()->create();
    $column = Column::factory()->create(['board_id' => $board->id]);
    $task = Task::factory()->create([
        'column_id' => $column->id,
        'title' => 'Old Title',
    ]);

    $service = new TaskService();
    $result = $service->updateTask($task, ['title' => 'New Title']);
    expect($result)->toBeTrue();

    $task->refresh();
    expect($task->title)->toBe('New Title');
});

it('deletes a task', function () {
    $board = Board::factory()->create();
    $column = Column::factory()->create(['board_id' => $board->id]);
    $task = Task::factory()->create(['column_id' => $column->id]);

    $service = new TaskService();
    $result = $service->deleteTask($task);
    expect($result)->toBeTrue();
    expect(Task::find($task->id))->toBeNull();
});

it('reorders tasks within a column', function () {
    $board = Board::factory()->create();
    $column = Column::factory()->create(['board_id' => $board->id]);

    $task1 = Task::factory()->create(['column_id' => $column->id, 'position' => 1]);
    $task2 = Task::factory()->create(['column_id' => $column->id, 'position' => 2]);

    $service = new TaskService();
    $reorderPayload = [
        ['id' => $task1->id, 'position' => 2, 'column_id' => $column->id],
        ['id' => $task2->id, 'position' => 1, 'column_id' => $column->id],
    ];

    $result = $service->reorderTasks($column, $reorderPayload);
    expect($result)->toBeTrue();

    $task1->refresh();
    $task2->refresh();
    expect($task1->position)->toBe(2);
    expect($task2->position)->toBe(1);
});
