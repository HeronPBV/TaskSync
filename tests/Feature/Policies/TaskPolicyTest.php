<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Board;
use App\Models\Column;
use App\Models\Task;
use App\Models\User;
use App\Policies\TaskPolicy;

uses(RefreshDatabase::class)->in(__DIR__);

/*
|--------------------------------------------------------------------------
| TaskPolicy Tests
|--------------------------------------------------------------------------
|
| Tests to ensure that only the board owner can view, create, update, and delete tasks.
|
*/

it('allows board owner to view a task', function () {
    $user = User::factory()->create();
    $board = Board::factory()->create(['user_id' => $user->id]);
    $column = Column::factory()->create(['board_id' => $board->id]);
    $task = Task::factory()->create(['column_id' => $column->id]);

    $policy = new TaskPolicy();
    expect($policy->view($user, $task))->toBeTrue();
});

it('denies non-owners from viewing a task', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $board = Board::factory()->create(['user_id' => $owner->id]);
    $column = Column::factory()->create(['board_id' => $board->id]);
    $task = Task::factory()->create(['column_id' => $column->id]);

    $policy = new TaskPolicy();
    expect($policy->view($otherUser, $task))->toBeFalse();
});

it('allows board owner to create a task', function () {
    $user = User::factory()->create();
    $board = Board::factory()->create(['user_id' => $user->id]);
    $column = Column::factory()->create(['board_id' => $board->id]);

    $policy = new TaskPolicy();
    expect($policy->create($user, $column))->toBeTrue();
});

it('denies non-owners from creating a task', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $board = Board::factory()->create(['user_id' => $owner->id]);
    $column = Column::factory()->create(['board_id' => $board->id]);

    $policy = new TaskPolicy();
    expect($policy->create($otherUser, $column))->toBeFalse();
});

it('allows board owner to update a task', function () {
    $user = User::factory()->create();
    $board = Board::factory()->create(['user_id' => $user->id]);
    $column = Column::factory()->create(['board_id' => $board->id]);
    $task = Task::factory()->create(['column_id' => $column->id]);

    $policy = new TaskPolicy();
    expect($policy->update($user, $task))->toBeTrue();
});

it('denies non-owners from updating a task', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $board = Board::factory()->create(['user_id' => $owner->id]);
    $column = Column::factory()->create(['board_id' => $board->id]);
    $task = Task::factory()->create(['column_id' => $column->id]);

    $policy = new TaskPolicy();
    expect($policy->update($otherUser, $task))->toBeFalse();
});

it('allows board owner to delete a task', function () {
    $user = User::factory()->create();
    $board = Board::factory()->create(['user_id' => $user->id]);
    $column = Column::factory()->create(['board_id' => $board->id]);
    $task = Task::factory()->create(['column_id' => $column->id]);

    $policy = new TaskPolicy();
    expect($policy->delete($user, $task))->toBeTrue();
});

it('denies non-owners from deleting a task', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $board = Board::factory()->create(['user_id' => $owner->id]);
    $column = Column::factory()->create(['board_id' => $board->id]);
    $task = Task::factory()->create(['column_id' => $column->id]);

    $policy = new TaskPolicy();
    expect($policy->delete($otherUser, $task))->toBeFalse();
});
