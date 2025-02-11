<?php

namespace App\Services;

use App\Exceptions\ServiceException;
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
     * @throws ServiceException
     */
    public function createBoard(array $data, $user): Board
    {
        $data['user_id'] = $user->id;
        $board = Board::create($data);
        if (!$board) {
            throw new ServiceException("Board creation failed.");
        }
        return $board;
    }

    /**
     * Update an existing board.
     *
     * @param Board $board
     * @param array $data
     * @return bool
     * @throws ServiceException
     */
    public function updateBoard(Board $board, array $data): bool
    {
        $result = $board->update($data);
        if (!$result) {
            throw new ServiceException("Board update failed.");
        }
        return $result;
    }

    /**
     * Delete (soft delete) a board.
     *
     * @param Board $board
     * @return bool|null
     * @throws ServiceException
     */
    public function deleteBoard(Board $board): ?bool
    {
        $result = $board->delete();
        if (!$result) {
            throw new ServiceException("Board deletion failed.");
        }
        return $result;
    }

    /**
     * Retrieve a board with its associated columns and tasks loaded.
     *
     * @param Board $board
     * @return Board
     */
    public function getBoardWithDetails(Board $board): Board
    {
        return $board->load('columns.tasks');
    }
}
