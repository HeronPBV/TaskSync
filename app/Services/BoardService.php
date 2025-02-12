<?php

namespace App\Services;

use App\Exceptions\ServiceException;
use App\Models\User;
use App\Models\Board;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class BoardService
{
    protected $cacheTTL = 3600; // 1 hour

    /**
     * Retrieve all boards for a given user, caching the result.
     *
     * @param User $user
     * @return Collection
     */
    public function getBoardsForUser($user): Collection
    {
        $cacheKey = "user:{$user->id}:boards";
        return Cache::remember($cacheKey, $this->cacheTTL, function () use ($user) {
            return Board::where('user_id', $user->id)
                ->with('columns.tasks')
                ->get();
        });
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

        $this->invalidateBoardCache($board);
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

        $this->invalidateBoardCache($board);
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

        $this->invalidateBoardCache($board);
        return $result;
    }

    /**
     * Retrieve a board with its associated columns and tasks loaded, caching the result.
     *
     * @param Board $board
     * @return Board
     */
    public function getBoardWithDetails(Board $board): Board
    {
        $cacheKey = "board:{$board->id}:details";
        return Cache::remember($cacheKey, $this->cacheTTL, function () use ($board) {
            return $board->load(['columns.tasks']);
        });
    }

    /**
     * Invalidate cache keys for a board.
     *
     * @param Board $board
     */
    protected function invalidateBoardCache(Board $board): void
    {
        Cache::forget("board:{$board->id}:details");
        Cache::forget("user:{$board->user_id}:boards");
        Cache::forget("board:{$board->id}:columns");
    }
}
