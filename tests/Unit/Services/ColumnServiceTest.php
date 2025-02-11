<?php

use App\Models\Board;
use App\Models\Column;
use App\Services\ColumnService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class)->in(__DIR__);

/*
|--------------------------------------------------------------------------
| ColumnService Unit Tests
|--------------------------------------------------------------------------
|
| These tests verify the creation, updating, deletion, and retrieval of columns,
| as well as ensuring that the position is calculated correctly.
|
*/

it('retrieves columns for a given board', function () {
    $board = Board::factory()->create();
    Column::factory()->count(3)->create(['board_id' => $board->id]);

    $service = new ColumnService();
    $columns = $service->getColumnsForBoard($board);

    expect($columns->count())->toBe(3);
});

it('creates a new column with correct position', function () {
    $board = Board::factory()->create();
    Column::factory()->create(['board_id' => $board->id, 'position' => 1]);

    $service = new ColumnService();
    $data = ['name' => 'New Column'];
    $column = $service->createColumn($board, $data);

    expect($column)->toBeInstanceOf(Column::class);
    expect($column->position)->toBe(2);
    expect($column->board_id)->toBe($board->id);
});

it('updates an existing column', function () {
    $board = Board::factory()->create();
    $column = Column::factory()->create(['board_id' => $board->id, 'name' => 'Old Name']);

    $service = new ColumnService();
    $result = $service->updateColumn($column, ['name' => 'New Name']);
    expect($result)->toBeTrue();

    $column->refresh();
    expect($column->name)->toBe('New Name');
});

it('deletes a column', function () {
    $board = Board::factory()->create();
    $column = Column::factory()->create(['board_id' => $board->id]);

    $service = new ColumnService();
    $result = $service->deleteColumn($column);
    expect($result)->toBeTrue();
    expect(Column::find($column->id))->toBeNull();
});
