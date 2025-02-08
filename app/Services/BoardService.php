<?php

namespace App\Services;

use App\Models\User;
use App\Models\Board;
use Illuminate\Database\Eloquent\Collection;

class BoardService
{
    /**
     * Retrieve all boards for a given user.
     *
     * @param User $user
     * @return Collection
     */
    public function getBoardsForUser($user): Collection
    {
        return Board::where('user_id', $user->id)->get();
    }

    /**
     * Create a new board for a user.
     *
     * @param array $data
     * @param User $user
     * @return Board
     */
    public function createBoard(array $data, $user): Board
    {
        $data['user_id'] = $user->id;
        return Board::create($data);
    }

    /**
     * Update an existing board.
     *
     * @param Board $board
     * @param array $data
     * @return bool
     */
    public function updateBoard(Board $board, array $data): bool
    {
        return $board->update($data);
    }

    /**
     * Delete (soft delete) a board.
     *
     * @param Board $board
     * @return bool|null
     */
    public function deleteBoard(Board $board): bool|null
    {
        return $board->delete();
    }
}
