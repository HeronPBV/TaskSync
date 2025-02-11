<?php

use App\Models\Task;
use App\Models\User;
use App\Models\Board;
use App\Models\Column;
use App\Services\BoardService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class)->in(__DIR__);

/*
|--------------------------------------------------------------------------
| BoardService Unit Tests
|--------------------------------------------------------------------------
|
| These tests verify that the BoardService methods perform the expected
| operations, and that the caching layer is working correctly.
|
*/

it('retrieves boards for a given user and caches the result', function () {
    $user = User::factory()->create();
    Board::factory()->count(3)->create(['user_id' => $user->id]);
    Board::factory()->count(2)->create();

    $service = new BoardService();
    Cache::flush();

    $boards = $service->getBoardsForUser($user);
    $cacheKey = "user:{$user->id}:boards";

    expect($boards->count())->toBe(3);
    expect(Cache::has($cacheKey))->toBeTrue();

    $board = Board::firstWhere('user_id', $user->id);
    $board->update(['name' => 'Updated Name']);

    $boardsCached = $service->getBoardsForUser($user);
    expect($boardsCached->first()->name)->not->toBe('Updated Name');
});

it('creates a new board and invalidates related cache keys', function () {
    $user = User::factory()->create();
    $service = new BoardService();
    Cache::flush();

    $service->getBoardsForUser($user);
    $cacheKeyBoards = "user:{$user->id}:boards";

    expect(Cache::has($cacheKeyBoards))->toBeTrue();

    $data = [
        'name' => 'Test Board',
        'description' => 'Test description',
    ];
    $board = $service->createBoard($data, $user);

    expect(Cache::has($cacheKeyBoards))->toBeFalse();

    $cacheKeyDetails = "board:{$board->id}:details";
    expect(Cache::has($cacheKeyDetails))->toBeFalse();
});

it('updates an existing board and invalidates related cache keys', function () {
    $user = User::factory()->create();
    $board = Board::factory()->create(['user_id' => $user->id, 'name' => 'Old Name']);
    $service = new BoardService();
    Cache::flush();

    $service->getBoardWithDetails($board);
    $service->getBoardsForUser($user);
    $cacheKeyDetails = "board:{$board->id}:details";
    $cacheKeyBoards = "user:{$user->id}:boards";

    expect(Cache::has($cacheKeyDetails))->toBeTrue();
    expect(Cache::has($cacheKeyBoards))->toBeTrue();

    $result = $service->updateBoard($board, ['name' => 'New Name']);
    expect($result)->toBeTrue();

    expect(Cache::has($cacheKeyDetails))->toBeFalse();
    expect(Cache::has($cacheKeyBoards))->toBeFalse();
});

it('deletes a board and invalidates related cache keys', function () {
    $user = User::factory()->create();
    $board = Board::factory()->create(['user_id' => $user->id]);
    $service = new BoardService();
    Cache::flush();

    $service->getBoardWithDetails($board);
    $service->getBoardsForUser($user);
    $cacheKeyDetails = "board:{$board->id}:details";
    $cacheKeyBoards = "user:{$user->id}:boards";
    expect(Cache::has($cacheKeyDetails))->toBeTrue();
    expect(Cache::has($cacheKeyBoards))->toBeTrue();

    $result = $service->deleteBoard($board);
    expect($result)->toBeTrue();
    expect(Board::find($board->id))->toBeNull();

    expect(Cache::has($cacheKeyDetails))->toBeFalse();
    expect(Cache::has($cacheKeyBoards))->toBeFalse();
});

it('retrieves a board with its columns and tasks loaded and caches the result', function () {
    $user = User::factory()->create();
    $board = Board::factory()->create(['user_id' => $user->id]);
    $column = Column::factory()->create(['board_id' => $board->id]);
    Task::factory()->create(['column_id' => $column->id]);

    $service = new BoardService();
    Cache::flush();

    $boardWithDetails = $service->getBoardWithDetails($board);
    $cacheKeyDetails = "board:{$board->id}:details";
    expect($boardWithDetails->relationLoaded('columns'))->toBeTrue();
    expect($boardWithDetails->columns->first()->relationLoaded('tasks'))->toBeTrue();
    expect(Cache::has($cacheKeyDetails))->toBeTrue();
});
