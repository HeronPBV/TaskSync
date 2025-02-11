<?php

use App\Models\Board;
use App\Models\Column;
use App\Services\ColumnService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

uses(RefreshDatabase::class)->in(__DIR__);

/*
|--------------------------------------------------------------------------
| ColumnService Unit Tests
|--------------------------------------------------------------------------
|
| These tests verify that the ColumnService methods perform the expected
| operations and properly handle caching.
|
*/

it('retrieves columns for a given board and caches the result', function () {
    $board = Board::factory()->create();
    Column::factory()->count(3)->create(['board_id' => $board->id]);

    $service = new ColumnService();
    Cache::flush();

    $columns = $service->getColumnsForBoard($board);
    $cacheKey = "board:{$board->id}:columns";
    expect($columns->count())->toBe(3);
    expect(Cache::has($cacheKey))->toBeTrue();
});

it('creates a new column and invalidates the board columns cache', function () {
    $board = Board::factory()->create();
    Column::factory()->create(['board_id' => $board->id, 'position' => 1]);

    $service = new ColumnService();
    Cache::flush();

    $service->getColumnsForBoard($board);
    $cacheKey = "board:{$board->id}:columns";
    expect(Cache::has($cacheKey))->toBeTrue();

    $data = ['name' => 'New Column'];
    $column = $service->createColumn($board, $data);
    expect($column)->toBeInstanceOf(Column::class);
    expect($column->position)->toBe(2);
    expect($column->board_id)->toBe($board->id);

    expect(Cache::has($cacheKey))->toBeFalse();
});

it('updates an existing column and invalidates the board columns cache', function () {
    $board = Board::factory()->create();
    $column = Column::factory()->create(['board_id' => $board->id, 'name' => 'Old Name']);

    $service = new ColumnService();
    Cache::flush();

    $service->getColumnsForBoard($board);
    $cacheKey = "board:{$board->id}:columns";
    expect(Cache::has($cacheKey))->toBeTrue();

    $result = $service->updateColumn($column, ['name' => 'New Name']);
    expect($result)->toBeTrue();
    $column->refresh();
    expect($column->name)->toBe('New Name');

    expect(Cache::has($cacheKey))->toBeFalse();
});

it('deletes a column and invalidates the board columns cache', function () {
    $board = Board::factory()->create();
    $column = Column::factory()->create(['board_id' => $board->id]);

    $service = new ColumnService();
    Cache::flush();

    $service->getColumnsForBoard($board);
    $cacheKey = "board:{$board->id}:columns";
    expect(Cache::has($cacheKey))->toBeTrue();

    $result = $service->deleteColumn($column);
    expect($result)->toBeTrue();
    expect(Column::find($column->id))->toBeNull();

    expect(Cache::has($cacheKey))->toBeFalse();
});
