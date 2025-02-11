<?php

namespace App\Events;

use App\Models\Board;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class BoardDeleted implements ShouldBroadcast
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
     */
    public function broadcastOn()
    {
        return new Channel('boards');
    }

    public function broadcastAs()
    {
        return 'BoardDeleted';
    }

    public function broadcastWith()
    {
        return [
            'board' => $this->board,
            'senderId' => $this->senderId,
        ];
    }
}
