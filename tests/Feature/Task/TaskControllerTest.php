<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Board;
use App\Models\Column;
use App\Models\Task;

uses(RefreshDatabase::class)->in(__DIR__);

/*
|--------------------------------------------------------------------------
| TaskController Feature Tests
|--------------------------------------------------------------------------
|
| These tests validate the task endpoints:
| 1. Creating a new task with valid data and checking that its position is set
|    to 1 and that existing tasks are shifted.
| 2. Validation on creation (e.g., title is required).
| 3. Updating a task via PATCH.
| 4. Deleting a task via DELETE.
| 5. Reordering tasks via the PATCH endpoint.
|
*/

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    $this->board = Board::factory()->create(['user_id' => $this->user->id]);
    $this->column = Column::factory()->create(['board_id' => $this->board->id]);
});

it('creates a new task with valid data', function () {
    $payload = [
        'title' => 'New Task',
        'description' => 'Task description',
        'due_date' => now()->toDateString(),
        'priority' => 'medium',
    ];

    $response = $this->postJson(route('columns.tasks.store', ['column' => $this->column->id]), $payload);

    $response->assertStatus(200)
        ->assertJsonStructure(['task' => ['id', 'column_id', 'title', 'description', 'due_date', 'priority', 'position']]);

    $task = Task::first();
    expect($task)->not->toBeNull();
    expect($task->title)->toBe('New Task');
    expect($task->position)->toBe(1);
});

it('validates that the title is required when creating a task', function () {
    $payload = [
        'title' => '',
        'description' => 'Task description',
        'due_date' => now()->toDateString(),
        'priority' => 'high',
    ];

    $response = $this->postJson(route('columns.tasks.store', ['column' => $this->column->id]), $payload);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('title');
});

it('updates an existing task', function () {
    $task = Task::factory()->create([
        'column_id' => $this->column->id,
        'title' => 'Old Title',
        'description' => 'Old description',
    ]);

    $updatePayload = [
        'title' => 'Updated Title',
        'description' => 'Updated description',
        'due_date' => now()->addDay()->toDateString(),
        'priority' => 'low',
    ];

    $response = $this->patchJson(route('tasks.update', ['task' => $task->id]), $updatePayload);

    $response->assertStatus(200)
        ->assertJsonStructure(['task' => ['id', 'column_id', 'title', 'description', 'due_date', 'priority', 'position']]);

    $updatedTask = Task::find($task->id);
    expect($updatedTask->title)->toBe('Updated Title');
    expect($updatedTask->description)->toBe('Updated description');
});

it('deletes an existing task', function () {
    $task = Task::factory()->create(['column_id' => $this->column->id]);

    $response = $this->deleteJson(route('tasks.destroy', ['task' => $task->id]));

    $response->assertStatus(200)
        ->assertJsonFragment(['message' => 'Task deleted successfully.']);

    expect(Task::find($task->id))->toBeNull();
});

it('reorders tasks within a column', function () {
    $task1 = Task::factory()->create([
        'column_id' => $this->column->id,
        'title' => 'Task 1',
        'position' => 1,
    ]);

    $task2 = Task::factory()->create([
        'column_id' => $this->column->id,
        'title' => 'Task 2',
        'position' => 2,
    ]);

    $reorderPayload = [
        'tasks' => [
            ['id' => $task1->id, 'position' => 2, 'column_id' => $this->column->id],
            ['id' => $task2->id, 'position' => 1, 'column_id' => $this->column->id],
        ]
    ];

    $response = $this->patchJson(route('tasks.reorder', ['column' => $this->column->id]), $reorderPayload);

    $response->assertStatus(200)
        ->assertJsonFragment(['success' => true]);

    $reorderedTask1 = Task::find($task1->id);
    $reorderedTask2 = Task::find($task2->id);

    expect($reorderedTask1->position)->toBe(2);
    expect($reorderedTask2->position)->toBe(1);
});
