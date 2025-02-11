<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Board;
use App\Events\BoardCreated;
use App\Events\BoardDeleted;
use App\Events\BoardUpdated;
use App\Services\BoardService;
use Illuminate\Support\Facades\Http;
use App\Http\Requests\StoreBoardRequest;
use App\Http\Requests\UpdateBoardRequest;
use Illuminate\Http\Request;


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

        $socketId = $request->header('X-Socket-ID');

        broadcast(new BoardCreated($board, $socketId))->toOthers();

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

        $socketId = $request->header('X-Socket-ID');

        broadcast(new BoardUpdated($board, $socketId))->toOthers();

        return response()->json([
            'board' => $board,
            'success' => true,
        ]);

    }


    public function destroy(Request $request, Board $board)
    {
        $this->authorize('delete', $board);

        $this->boardService->deleteBoard($board);

        $socketId = $request->header('X-Socket-ID');

        broadcast(new BoardDeleted($board, $socketId))->toOthers();

        return response()->json(['success' => 'Board deleted successfully.']);
    }
}
