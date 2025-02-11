<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Board;
use App\Models\Column;
use App\Models\User;
use App\Policies\ColumnPolicy;

uses(RefreshDatabase::class)->in(__DIR__);

/*
|--------------------------------------------------------------------------
| ColumnPolicy Tests
|--------------------------------------------------------------------------
|
| Tests to ensure that only the board owner can view, create, update, and delete columns.
|
*/

it('allows board owner to view a column', function () {
    $user = User::factory()->create();
    $board = Board::factory()->create(['user_id' => $user->id]);
    $column = Column::factory()->create(['board_id' => $board->id]);

    $policy = new ColumnPolicy();
    expect($policy->view($user, $column))->toBeTrue();
});

it('denies non-owners from viewing a column', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $board = Board::factory()->create(['user_id' => $owner->id]);
    $column = Column::factory()->create(['board_id' => $board->id]);

    $policy = new ColumnPolicy();
    expect($policy->view($otherUser, $column))->toBeFalse();
});

it('allows board owner to create a column', function () {
    $user = User::factory()->create();
    $board = Board::factory()->create(['user_id' => $user->id]);

    $policy = new ColumnPolicy();
    expect($policy->create($user, $board))->toBeTrue();
});

it('denies non-owners from creating a column', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $board = Board::factory()->create(['user_id' => $owner->id]);

    $policy = new ColumnPolicy();
    expect($policy->create($otherUser, $board))->toBeFalse();
});

it('allows board owner to update a column', function () {
    $user = User::factory()->create();
    $board = Board::factory()->create(['user_id' => $user->id]);
    $column = Column::factory()->create(['board_id' => $board->id]);

    $policy = new ColumnPolicy();
    expect($policy->update($user, $column))->toBeTrue();
});

it('denies non-owners from updating a column', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $board = Board::factory()->create(['user_id' => $owner->id]);
    $column = Column::factory()->create(['board_id' => $board->id]);

    $policy = new ColumnPolicy();
    expect($policy->update($otherUser, $column))->toBeFalse();
});

it('allows board owner to delete a column', function () {
    $user = User::factory()->create();
    $board = Board::factory()->create(['user_id' => $user->id]);
    $column = Column::factory()->create(['board_id' => $board->id]);

    $policy = new ColumnPolicy();
    expect($policy->delete($user, $column))->toBeTrue();
});

it('denies non-owners from deleting a column', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $board = Board::factory()->create(['user_id' => $owner->id]);
    $column = Column::factory()->create(['board_id' => $board->id]);

    $policy = new ColumnPolicy();
    expect($policy->delete($otherUser, $column))->toBeFalse();
});
