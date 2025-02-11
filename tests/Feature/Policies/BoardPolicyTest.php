<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Board;
use App\Models\User;
use App\Policies\BoardPolicy;

uses(RefreshDatabase::class)->in(__DIR__);

/*
|--------------------------------------------------------------------------
| BoardPolicy Tests
|--------------------------------------------------------------------------
|
| These tests ensure that the BoardPolicy methods work correctly.
|
*/

it('allows any user to view any board', function () {
    $user = User::factory()->create();
    $board = Board::factory()->create();

    $policy = new BoardPolicy();
    expect($policy->view($user, $board))->toBeTrue();
});

it('allows any authenticated user to create a board', function () {
    $user = User::factory()->create();

    $policy = new BoardPolicy();
    // Neste exemplo, criamos um board, mas a policy "create" não precisa de um board já criado
    expect($policy->create($user))->toBeTrue();
});

it('allows board owner to update the board', function () {
    $owner = User::factory()->create();
    $board = Board::factory()->create(['user_id' => $owner->id]);

    $policy = new BoardPolicy();
    expect($policy->update($owner, $board))->toBeTrue();
});

it('denies non-owners from updating the board', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $board = Board::factory()->create(['user_id' => $owner->id]);

    $policy = new BoardPolicy();
    expect($policy->update($otherUser, $board))->toBeFalse();
});

it('allows board owner to delete the board', function () {
    $owner = User::factory()->create();
    $board = Board::factory()->create(['user_id' => $owner->id]);

    $policy = new BoardPolicy();
    expect($policy->delete($owner, $board))->toBeTrue();
});

it('denies non-owners from deleting the board', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $board = Board::factory()->create(['user_id' => $owner->id]);

    $policy = new BoardPolicy();
    expect($policy->delete($otherUser, $board))->toBeFalse();
});
