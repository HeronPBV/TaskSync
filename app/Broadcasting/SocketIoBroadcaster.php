<?php

namespace App\Broadcasting;

use Illuminate\Contracts\Broadcasting\Broadcaster;
use Illuminate\Broadcasting\BroadcastException;
use Illuminate\Support\Facades\Http;

class SocketIoBroadcaster implements Broadcaster
{
    //Design Pattern: Adapter
    protected $socketServerUrl;

    public function __construct()
    {
        $this->socketServerUrl = config('broadcasting.connections.socketio.url');
    }

    public function auth($request)
    {
        return [];
    }


    public function validAuthenticationResponse($request, $result)
    {
        return $result;
    }

    /**
     * Broadcast the given event.
     *
     * @param  array  $channels
     * @param  string  $event
     * @param  array  $payload
     * @return void
     *
     * @throws BroadcastException
     */
    public function broadcast(array $channels, $event, array $payload = [])
    {
        $senderId = $payload['senderId'] ?? null;
        if (isset($payload['senderId'])) {
            unset($payload['senderId']);
        }

        $data = [
            'channels' => $channels,
            'event' => $event,
            'payload' => $payload,
            'clientId' => $senderId,
        ];

        $response = Http::post($this->socketServerUrl . '/broadcast', $data);

        if (!$response->successful()) {
            throw new BroadcastException("Failed to broadcast event via Socket.IO. Response: " . $response->body());
        }
    }
}
