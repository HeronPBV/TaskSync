<?php

namespace App\Events;

use App\Models\Board;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class BoardCreated implements ShouldBroadcast
{
    use SerializesModels;

    public $board;
    public $senderId;

    /**
     * Create a new event instance.
     *
     * @param Board $board
     * @param int|null $senderId
     */
    public function __construct(Board $board, $senderId = null)
    {
        $this->board = $board;
        $this->senderId = $senderId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        // Podemos usar o mesmo canal "boards" para ambos os eventos
        return new Channel('boards');
    }

    public function broadcastAs()
    {
        return 'BoardCreated';
    }

    public function broadcastWith()
    {
        return [
            'board' => $this->board,
            'senderId' => $this->senderId,
        ];
    }
}
