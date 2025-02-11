<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Board;
use App\Models\Column;
use Illuminate\Support\Facades\Gate;

uses(RefreshDatabase::class)->in(__DIR__);

/*
|--------------------------------------------------------------------------
| ColumnController Endpoint Tests
|--------------------------------------------------------------------------
|
| These tests verify the creation, update, and deletion of columns via the
| ColumnController. They check for proper HTTP status codes, JSON responses,
| business logic (like position assignment) and authorization behavior.
|
*/

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('creates a new column for a board with proper position', function () {
    $board = Board::factory()->create(['user_id' => $this->user->id]);

    $payload = [
        'name' => 'New Column',
    ];

    $response = $this->postJson(route('boards.columns.store', ['board' => $board->id]), $payload);

    $response->assertStatus(200)
        ->assertJsonStructure(['column' => ['id', 'name', 'board_id', 'position']]);

    $column = Column::first();
    expect($column)->not->toBeNull();
    expect($column->name)->toBe('New Column');
    expect($column->board_id)->toBe($board->id);
    expect($column->position)->toBe(1);
});

it('updates an existing column and returns the updated column', function () {
    $board = Board::factory()->create(['user_id' => $this->user->id]);
    $column = Column::factory()->create([
        'board_id' => $board->id,
        'name' => 'Old Column Name',
    ]);

    $updatePayload = [
        'name' => 'Updated Column Name',
    ];

    $response = $this->patchJson(route('columns.update', ['column' => $column->id]), $updatePayload);

    $response->assertStatus(200)
        ->assertJsonStructure(['column' => ['id', 'name', 'board_id', 'position']]);

    $updatedColumn = Column::find($column->id);
    expect($updatedColumn->name)->toBe('Updated Column Name');
});

it('allows board owner to delete a column', function () {
    $board = Board::factory()->create(['user_id' => $this->user->id]);
    $column = Column::factory()->create(['board_id' => $board->id]);

    $response = $this->deleteJson(route('columns.destroy', ['column' => $column->id]));

    $response->assertStatus(200)
        ->assertJsonFragment(['success' => 'Column deleted successfully.']);

    expect(Column::find($column->id))->toBeNull();
});

it('denies non-owners from deleting a column', function () {
    $owner = User::factory()->create();
    $board = Board::factory()->create(['user_id' => $owner->id]);
    $column = Column::factory()->create(['board_id' => $board->id]);

    $otherUser = User::factory()->create();
    $this->actingAs($otherUser);

    $response = $this->deleteJson(route('columns.destroy', ['column' => $column->id]));

    $response->assertStatus(403);

    expect(Column::find($column->id))->not->toBeNull();
});
