<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{

    public function view(User $user, Task $task): bool
    {
        return $user->id === $task->column->board->user_id;
    }

    public function create(User $user, $column): bool
    {
        return $user->id === $column->board->user_id;
    }

    public function update(User $user, Task $task): bool
    {
        return $user->id === $task->column->board->user_id;
    }

    public function delete(User $user, Task $task): bool
    {
        return $user->id === $task->column->board->user_id;
    }

}
