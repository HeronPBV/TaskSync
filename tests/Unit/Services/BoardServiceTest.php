<?php

use App\Models\Task;
use App\Models\User;
use App\Models\Board;
use App\Models\Column;
use App\Services\BoardService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class)->in(__DIR__);

/*
|--------------------------------------------------------------------------
| BoardService Unit Tests
|--------------------------------------------------------------------------
|
| These tests verify that the BoardService methods perform the expected
| operations, such as creating, updating, deleting, retrieving boards, and
| loading related columns and tasks.
|
*/

it('retrieves boards for a given user', function () {
    $user = User::factory()->create();
    Board::factory()->count(3)->create(['user_id' => $user->id]);
    Board::factory()->count(2)->create();

    $service = new BoardService();
    $boards = $service->getBoardsForUser($user);

    expect($boards->count())->toBe(3);
});

it('creates a new board for a user', function () {
    $user = User::factory()->create();
    $service = new BoardService();
    $data = [
        'name' => 'Test Board',
        'description' => 'Test description',
    ];
    $board = $service->createBoard($data, $user);

    expect($board)->toBeInstanceOf(Board::class);
    expect($board->user_id)->toBe($user->id);
    expect($board->name)->toBe('Test Board');
    expect($board->description)->toBe('Test description');
});

it('updates an existing board', function () {
    $user = User::factory()->create();
    $board = Board::factory()->create(['user_id' => $user->id, 'name' => 'Old Name']);
    $service = new BoardService();

    $result = $service->updateBoard($board, ['name' => 'New Name']);
    expect($result)->toBeTrue();

    $board->refresh();
    expect($board->name)->toBe('New Name');
});

it('deletes a board', function () {
    $user = User::factory()->create();
    $board = Board::factory()->create(['user_id' => $user->id]);
    $service = new BoardService();

    $result = $service->deleteBoard($board);
    expect($result)->toBeTrue();
    expect(Board::find($board->id))->toBeNull();
});

it('retrieves a board with its columns and tasks loaded', function () {
    $user = User::factory()->create();
    $board = Board::factory()->create(['user_id' => $user->id]);
    $column = Column::factory()->create(['board_id' => $board->id]);
    Task::factory()->create(['column_id' => $column->id]);

    $service = new BoardService();
    $boardWithDetails = $service->getBoardWithDetails($board);

    expect($boardWithDetails->relationLoaded('columns'))->toBeTrue();
    expect($boardWithDetails->columns->first()->relationLoaded('tasks'))->toBeTrue();
});
