<?php

use App\Models\Board;
use App\Models\Column;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

uses(RefreshDatabase::class)->in(__DIR__);

/*
|--------------------------------------------------------------------------
| TaskService Unit Tests
|--------------------------------------------------------------------------
|
| These tests validate the task operations and check that the caching layer
| is correctly updated on reads and invalidated on writes.
|
*/

it('retrieves tasks for a given column and caches the result', function () {
    $board = Board::factory()->create();
    $column = Column::factory()->create(['board_id' => $board->id]);
    Task::factory()->count(3)->create(['column_id' => $column->id]);

    $service = new TaskService();
    Cache::flush();

    $tasks = $service->getTasksForColumn($column);
    $cacheKey = "column:{$column->id}:tasks";
    expect($tasks->count())->toBe(3);
    expect(Cache::has($cacheKey))->toBeTrue();
});

it('creates a new task and invalidates the column tasks cache', function () {
    $board = Board::factory()->create();
    $column = Column::factory()->create(['board_id' => $board->id]);

    $existingTask = Task::factory()->create(['column_id' => $column->id, 'position' => 1]);

    $service = new TaskService();
    Cache::flush();

    $service->getTasksForColumn($column);
    $cacheKey = "column:{$column->id}:tasks";
    expect(Cache::has($cacheKey))->toBeTrue();

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

    expect(Cache::has($cacheKey))->toBeFalse();
});

it('updates an existing task and invalidates the column tasks cache', function () {
    $board = Board::factory()->create();
    $column = Column::factory()->create(['board_id' => $board->id]);
    $task = Task::factory()->create([
        'column_id' => $column->id,
        'title' => 'Old Title',
    ]);

    $service = new TaskService();
    Cache::flush();

    $service->getTasksForColumn($column);
    $cacheKey = "column:{$column->id}:tasks";
    expect(Cache::has($cacheKey))->toBeTrue();

    $result = $service->updateTask($task, ['title' => 'New Title']);
    expect($result)->toBeTrue();

    $task->refresh();
    expect($task->title)->toBe('New Title');

    expect(Cache::has($cacheKey))->toBeFalse();
});

it('deletes a task and invalidates the column tasks cache', function () {
    $board = Board::factory()->create();
    $column = Column::factory()->create(['board_id' => $board->id]);
    $task = Task::factory()->create(['column_id' => $column->id]);

    $service = new TaskService();
    Cache::flush();

    $service->getTasksForColumn($column);
    $cacheKey = "column:{$column->id}:tasks";
    expect(Cache::has($cacheKey))->toBeTrue();

    $result = $service->deleteTask($task);
    expect($result)->toBeTrue();
    expect(Task::find($task->id))->toBeNull();

    expect(Cache::has($cacheKey))->toBeFalse();
});

it('reorders tasks within a column and invalidates the column tasks cache', function () {
    $board = Board::factory()->create();
    $column = Column::factory()->create(['board_id' => $board->id]);

    $task1 = Task::factory()->create(['column_id' => $column->id, 'position' => 1]);
    $task2 = Task::factory()->create(['column_id' => $column->id, 'position' => 2]);

    $service = new TaskService();
    Cache::flush();

    $service->getTasksForColumn($column);
    $cacheKey = "column:{$column->id}:tasks";
    expect(Cache::has($cacheKey))->toBeTrue();

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

    expect(Cache::has($cacheKey))->toBeFalse();
});
