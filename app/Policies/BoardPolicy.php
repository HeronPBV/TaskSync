<?php

namespace App\Policies;

use App\Models\Board;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BoardPolicy
{
    public function view(User $user, Board $board): bool
    {
        // All users can see all boards, just to simplify demonstrate realtime updates
        return true;
    }

    public function create(User $user): bool
    {
        //Any authenticated user can create a board
        return true;
    }

    public function update(User $user, Board $board): bool
    {
        return $user->id === $board->user_id;
    }

    public function delete(User $user, Board $board): bool
    {
        return $user->id === $board->user_id;
    }
}
