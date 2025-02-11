<?php

use App\Models\Task;
use App\Models\User;
use App\Models\Board;
use App\Models\Column;
use App\Services\TaskService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class)->in(__DIR__);

/*
|--------------------------------------------------------------------------
| Edge Cases & Validation Tests
|--------------------------------------------------------------------------
|
| These tests validate that the API endpoints correctly handle invalid or missing
| data, simulate error conditions and handle empty collections (e.g. reordering an
| empty column), thereby ensuring that the system behaves robustly in edge cases.
|
*/

it('returns validation error when creating a board without a name', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    // "name" is required but omitted.
    $data = [
        'description' => 'Board without a name',
    ];

    $response = $this->postJson(route('boards.store'), $data);
    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['name']);
});

it('returns validation error when updating a board with an empty name', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    $board = Board::factory()->create(['user_id' => $user->id]);

    $data = [
        'name' => '',
        'description' => 'Updated description',
    ];

    $response = $this->patchJson(route('boards.update', ['board' => $board->id]), $data);
    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['name']);
});

it('returns validation error when creating a column without a name', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    $board = Board::factory()->create(['user_id' => $user->id]);

    // "name" is required for a column.
    $data = [];
    $response = $this->postJson(route('boards.columns.store', ['board' => $board->id]), $data);
    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['name']);
});

it('returns validation error when updating a column with an empty name', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    $board = Board::factory()->create(['user_id' => $user->id]);
    $column = Column::factory()->create(['board_id' => $board->id, 'name' => 'Original Name']);

    $data = ['name' => ''];
    $response = $this->patchJson(route('columns.update', ['column' => $column->id]), $data);
    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['name']);
});

it('returns validation error when creating a task without a title', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    $board = Board::factory()->create(['user_id' => $user->id]);
    $column = Column::factory()->create(['board_id' => $board->id]);

    // "title" is required for a task.
    $data = [
        'description' => 'Task without title',
        'due_date' => now()->toDateString(),
        'priority' => 'high',
    ];

    $response = $this->postJson(route('columns.tasks.store', ['column' => $column->id]), $data);
    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['title']);
});

it('handles a reorder request for an empty column gracefully', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    $board = Board::factory()->create(['user_id' => $user->id]);
    $column = Column::factory()->create(['board_id' => $board->id]);

    // Even if there are no tasks, the endpoint should return success.
    $payload = ['tasks' => []];
    $response = $this->patchJson(route('tasks.reorder', ['column' => $column->id]), $payload);
    $response->assertStatus(200);
    $response->assertJson(['success' => true]);
});

it('returns an error when reordering tasks with an invalid payload', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    $board = Board::factory()->create(['user_id' => $user->id]);
    $column = Column::factory()->create(['board_id' => $board->id]);

    // Pass an invalid payload (missing required keys such as "id" or "position").
    $payload = [
        'tasks' => [
            ['invalid_key' => 'invalid_value']
        ]
    ];

    $response = $this->patchJson(route('tasks.reorder', ['column' => $column->id]), $payload);
    $response->assertStatus(422);
});

it('handles a DB error during task update gracefully', function () {
    // To simulate a database failure, we will make a partial mock of TaskService to force an exception on updateTask.

    $user = User::factory()->create();
    $this->actingAs($user);
    $board = Board::factory()->create(['user_id' => $user->id]);
    $column = Column::factory()->create(['board_id' => $board->id]);
    $task = Task::factory()->create(['column_id' => $column->id, 'title' => 'Old Title']);

    $serviceMock = Mockery::mock(TaskService::class)->makePartial();
    $serviceMock->shouldReceive('updateTask')
        ->once()
        ->with(
            Mockery::on(function ($passedTask) use ($task) {
                return $passedTask instanceof Task && $passedTask->id === $task->id;
            }),
            Mockery::any()
        )
        ->andThrow(new \Exception("DB error"));

    app()->instance(TaskService::class, $serviceMock);

    $data = ['title' => 'New Title'];
    $response = $this->patchJson(route('tasks.update', ['task' => $task->id]), $data);
    $response->assertStatus(500);
});
