<?php

namespace App\Policies;

use App\Models\Column;
use App\Models\User;

class ColumnPolicy
{

    public function view(User $user, Column $column): bool
    {
        return $user->id === $column->board->user_id;
    }

    public function create(User $user, $board): bool
    {
        return $user->id === $board->user_id;
    }

    public function update(User $user, Column $column): bool
    {
        return $user->id === $column->board->user_id;
    }

    public function delete(User $user, Column $column): bool
    {
        return $user->id === $column->board->user_id;
    }

}
