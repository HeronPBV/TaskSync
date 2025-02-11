<?php

use App\Broadcasting\SocketIoBroadcaster;
use Illuminate\Support\Facades\Http;

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
