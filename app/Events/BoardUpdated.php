<?php

namespace App\Events;

use App\Models\Board;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class BoardUpdated implements ShouldBroadcast
{
    use SerializesModels;

    public $board;
    public $updatedBy;

    /**
     * Create a new event instance.
     *
     * @param Board $board
     * @param int|null $updatedBy
     */
    public function __construct(Board $board, $updatedBy = null)
    {
        $this->board = $board;
        $this->updatedBy = $updatedBy;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('boards');
    }

    public function broadcastAs()
    {
        return 'BoardUpdated';
    }
}
