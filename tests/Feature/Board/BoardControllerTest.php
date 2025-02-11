<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Board;
use App\Events\BoardCreated;
use App\Events\BoardUpdated;
use App\Events\BoardDeleted;
use Illuminate\Support\Facades\Event;

uses(RefreshDatabase::class)->in(__DIR__);

/*
|--------------------------------------------------------------------------
| BoardController Feature Tests
|--------------------------------------------------------------------------
|
| These tests cover the CRUD operations for boards and ensure that the correct
| broadcast events (BoardCreated, BoardUpdated, BoardDeleted) are dispatched
| with the correct payload (including the sender's socket ID).
|
*/

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    $this->socketId = 'test-socket-id';
});

it('creates a new board and broadcasts BoardCreated event', function () {
    Event::fake();

    $payload = [
        'name' => 'Test Board',
        'description' => 'This is a test board.',
    ];

    $response = $this->withHeaders([
        'X-Socket-ID' => $this->socketId,
    ])->postJson(route('boards.store'), $payload);

    $response->assertStatus(200)
        ->assertJsonStructure(['board' => ['id', 'name', 'description']]);

    $board = Board::first();
    expect($board->name)->toBe($payload['name']);

    Event::assertDispatched(BoardCreated::class, function ($event) use ($board) {
        return $event->board->id === $board->id && $event->senderId === $this->socketId;
    });
});

it('updates a board and broadcasts BoardUpdated event', function () {
    Event::fake();

    $board = Board::factory()->create(['user_id' => $this->user->id]);
    $payload = [
        'name' => 'Updated Board Name',
        'description' => 'Updated description.',
    ];

    $response = $this->withHeaders([
        'X-Socket-ID' => $this->socketId,
    ])->patchJson(route('boards.update', $board->id), $payload);

    $response->assertStatus(200)
        ->assertJsonFragment(['success' => true]);

    $updatedBoard = Board::find($board->id);
    expect($updatedBoard->name)->toBe($payload['name']);

    Event::assertDispatched(BoardUpdated::class, function ($event) use ($board) {
        return $event->board->id === $board->id && $event->senderId === $this->socketId;
    });
});

it('deletes a board and broadcasts BoardDeleted event', function () {
    Event::fake();

    $board = Board::factory()->create(['user_id' => $this->user->id]);

    $response = $this->withHeaders([
        'X-Socket-ID' => $this->socketId,
    ])->deleteJson(route('boards.destroy', $board->id));

    $response->assertStatus(200)
        ->assertJsonFragment(['success' => 'Board deleted successfully.']);

    expect(Board::find($board->id))->toBeNull();

    Event::assertDispatched(BoardDeleted::class, function ($event) use ($board) {
        return $event->board->id === $board->id && $event->senderId === $this->socketId;
    });
});
