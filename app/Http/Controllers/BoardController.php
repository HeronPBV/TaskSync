<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBoardRequest;
use App\Http\Requests\UpdateBoardRequest;
use App\Models\Board;
use App\Services\BoardService;
use Inertia\Inertia;

class BoardController extends Controller
{
    protected BoardService $boardService;

    public function __construct(BoardService $boardService)
    {
        $this->boardService = $boardService;
    }

    public function create()
    {
        return Inertia::render('Boards/Create');
    }

    public function store(StoreBoardRequest $request)
    {
        $board = $this->boardService->createBoard($request->validated(), auth()->user());

        return response()->json(['board' => $board]);

    }

    public function show(Board $board)
    {
        $this->authorize('view', $board);

        $boardWithDetails = $this->boardService->getBoardWithDetails($board);

        return Inertia::render('Boards/Show', [
            'board' => $boardWithDetails,
        ]);
    }

    public function edit(Board $board)
    {
        $this->authorize('update', $board);

        return Inertia::render('Boards/Edit', [
            'board' => $board,
        ]);
    }

    public function update(UpdateBoardRequest $request, Board $board)
    {
        $this->boardService->updateBoard($board, $request->validated());

        return response()->json([
            'board' => $board,
            'success' => true,
        ]);

    }


    public function destroy(Board $board)
    {
        $this->authorize('delete', $board);

        $this->boardService->deleteBoard($board);

        return response()->json(['success' => 'Board deleted successfully.']);
    }
}
