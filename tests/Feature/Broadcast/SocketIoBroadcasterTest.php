<?php

use App\Broadcasting\SocketIoBroadcaster;
use Illuminate\Broadcasting\BroadcastException;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    config(['broadcasting.connections.socketio.url' => 'http://localhost:6001']);
});

it('sends correct payload to the broadcast endpoint', function () {
    Http::fake([
        '*' => Http::response(['message' => 'Event broadcasted successfully'], 200)
    ]);

    $broadcaster = new SocketIoBroadcaster();
    $channels = ['boards'];
    $event = 'BoardCreated';
    $payload = [
        'board' => [
            'id' => 1,
            'name' => 'Test Board',
            'description' => 'Test description'
        ],
        'senderId' => 'socket-123'
    ];

    $broadcaster->broadcast($channels, $event, $payload);

    Http::assertSent(function ($request) use ($channels, $event) {
        $data = $request->data();
        return $data['channels'] === $channels
            && $data['event'] === $event
            && $data['payload'] === [
                'board' => [
                    'id' => 1,
                    'name' => 'Test Board',
                    'description' => 'Test description'
                ]
            ]
            && $data['clientId'] === 'socket-123';
    });
});

it('sends payload with null clientId when senderId is not provided', function () {
    Http::fake([
        '*' => Http::response(['message' => 'Event broadcasted successfully'], 200)
    ]);

    $broadcaster = new SocketIoBroadcaster();
    $channels = ['boards'];
    $event = 'BoardCreated';
    $payload = [
        'board' => [
            'id' => 2,
            'name' => 'Another Board',
            'description' => 'Another description'
        ]
    ]; //senderId not included


    $broadcaster->broadcast($channels, $event, $payload);

    Http::assertSent(function ($request) use ($channels, $event) {
        $data = $request->data();
        return $data['channels'] === $channels
            && $data['event'] === $event
            && $data['payload'] === [
                'board' => [
                    'id' => 2,
                    'name' => 'Another Board',
                    'description' => 'Another description'
                ]
            ]
            && $data['clientId'] === null;
    });
});

it('throws a BroadcastException when the broadcast endpoint returns an error', function () {
    Http::fake([
        '*' => Http::response(['error' => 'Server error'], 500)
    ]);

    $broadcaster = new SocketIoBroadcaster();
    $channels = ['boards'];
    $event = 'BoardUpdated';
    $payload = [
        'board' => [
            'id' => 3,
            'name' => 'Error Board',
            'description' => 'This should fail'
        ],
        'senderId' => 'socket-456'
    ];

    expect(fn() => $broadcaster->broadcast($channels, $event, $payload))
        ->toThrow(BroadcastException::class, 'Failed to broadcast event via Socket.IO. Response: {"error":"Server error"}');
});

it('auth returns an empty array', function () {
    $broadcaster = new SocketIoBroadcaster();
    expect($broadcaster->auth(null))->toBe([]);
});

it('validAuthenticationResponse returns the result as is', function () {
    $broadcaster = new SocketIoBroadcaster();
    $result = ['foo' => 'bar'];
    expect($broadcaster->validAuthenticationResponse(null, $result))->toBe($result);
});
