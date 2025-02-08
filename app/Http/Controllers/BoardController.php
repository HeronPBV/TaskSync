<?php

namespace App\Http\Controllers;

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

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $board = Board::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('boards.show', $board->id)
            ->with('success', 'Board created successfully.');
    }

    public function show(Board $board)
    {
        if ($board->user_id !== auth()->id()) {
            abort(403);
        }

        return Inertia::render('Boards/Show', [
            'board' => $board,
        ]);
    }

    public function edit(Board $board)
    {
        if ($board->user_id !== auth()->id()) {
            abort(403);
        }

        return Inertia::render('Boards/Edit', [
            'board' => $board,
        ]);
    }

    public function update(Request $request, Board $board)
    {
        if ($board->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $board->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('boards.show', $board->id)
            ->with('success', 'Board updated successfully.');
    }

    public function destroy(Board $board)
    {
        if ($board->user_id !== auth()->id()) {
            abort(403);
        }

        $board->delete();

        return redirect()->route('boards.index')
            ->with('success', 'Board deleted successfully.');
    }
}
