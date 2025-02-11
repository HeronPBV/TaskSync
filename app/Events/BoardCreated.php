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

    /**
     * Create a new event instance.
     *
     * @param Board $board
     */
    public function __construct(Board $board)
    {
        $this->board = $board;
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
}
