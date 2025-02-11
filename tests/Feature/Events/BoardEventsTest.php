<?php

use App\Models\Board;
use App\Events\BoardCreated;
use App\Events\BoardUpdated;
use App\Events\BoardDeleted;

it('returns correct broadcast payload for BoardCreated event', function () {
    $board = Board::factory()->make([
        'id' => 1,
        'name' => 'Test Board',
        'description' => 'Board description'
    ]);

    $event = new BoardCreated($board, 'socket-123');
    $payload = $event->broadcastWith();

    expect($payload)->toMatchArray([
        'board' => $board->toArray(),
        'senderId' => 'socket-123'
    ]);
});

it('returns correct broadcast payload for BoardUpdated event', function () {
    $board = Board::factory()->make([
        'id' => 2,
        'name' => 'Updated Board',
        'description' => 'Updated description'
    ]);

    $event = new BoardUpdated($board, 'socket-456');
    $payload = $event->broadcastWith();

    expect($payload)->toMatchArray([
        'board' => $board->toArray(),
        'senderId' => 'socket-456'
    ]);
});

it('returns correct broadcast payload for BoardDeleted event', function () {
    $board = Board::factory()->make([
        'id' => 3,
        'name' => 'Deleted Board',
        'description' => 'Board to be deleted'
    ]);

    $event = new BoardDeleted($board, 'socket-789');
    $payload = $event->broadcastWith();

    expect($payload)->toMatchArray([
        'board' => $board->toArray(),
        'senderId' => 'socket-789'
    ]);
});
