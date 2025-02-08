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

    public function index()
    {
        $boards = $this->boardService->getBoardsForUser(auth()->user());

        return Inertia::render('Boards/Index', [
            'boards' => $boards,
        ]);
    }

    public function create()
    {
        return Inertia::render('Boards/Create');
    }

    public function store(StoreBoardRequest $request)
    {
        $board = $this->boardService->createBoard($request->validated(), auth()->user());

        return redirect()->route('boards.show', $board)
            ->with('success', 'Board created successfully.');
    }

    public function show(Board $board)
    {
        $this->authorize('view', $board);

        return Inertia::render('Boards/Show', [
            'board' => $board,
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

        return redirect()->route('boards.show', $board)
            ->with('success', 'Board updated successfully.');
    }

    public function destroy(Board $board)
    {
        $this->authorize('delete', $board);

        $this->boardService->deleteBoard($board);

        return redirect()->route('boards.index')
            ->with('success', 'Board deleted successfully.');
    }
}
