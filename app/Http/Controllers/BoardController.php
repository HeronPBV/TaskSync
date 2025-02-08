<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBoardRequest;
use App\Http\Requests\UpdateBoardRequest;
use App\Models\Board;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BoardController extends Controller
{

    public function index()
    {
        $boards = Board::where('user_id', auth()->id())->get();

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
        $board = Board::create([
            'user_id' => auth()->id(),
            'name' => $request->validated()['name'],
            'description' => $request->validated()['description'] ?? null,
        ]);

        return redirect()->route('boards.show', $board->id)
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
        $board->update($request->validated());

        return redirect()->route('boards.show', $board->id)
            ->with('success', 'Board updated successfully.');
    }

    public function destroy(Board $board)
    {
        $this->authorize('delete', $board);

        $board->delete();

        return redirect()->route('boards.index')
            ->with('success', 'Board deleted successfully.');
    }

}
