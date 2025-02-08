<?php

namespace App\Http\Controllers;

use App\Models\Column;
use App\Models\Board;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ColumnController extends Controller
{

    public function index(Board $board)
    {
        if ($board->user_id !== auth()->id()) {
            abort(403);
        }

        $columns = $board->columns()->get();

        return Inertia::render('Columns/Index', [
            'board' => $board,
            'columns' => $columns,
        ]);
    }

    public function create(Board $board)
    {
        if ($board->user_id !== auth()->id()) {
            abort(403);
        }

        return Inertia::render('Columns/Create', [
            'board' => $board,
        ]);
    }

    public function store(Request $request, Board $board)
    {
        if ($board->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'nullable|integer',
        ]);

        $column = $board->columns()->create([
            'name' => $request->name,
            'position' => $request->position ?? 0,
        ]);

        return redirect()->route('boards.show', $board->id)
            ->with('success', 'Column created successfully.');
    }


    public function edit(Column $column)
    {
        if ($column->board->user_id !== auth()->id()) {
            abort(403);
        }

        return Inertia::render('Columns/Edit', [
            'column' => $column,
        ]);
    }


    public function update(Request $request, Column $column)
    {
        if ($column->board->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'nullable|integer',
        ]);

        $column->update([
            'name' => $request->name,
            'position' => $request->position ?? $column->position,
        ]);

        return redirect()->route('boards.show', $column->board->id)
            ->with('success', 'Column updated successfully.');
    }

    public function destroy(Column $column)
    {
        if ($column->board->user_id !== auth()->id()) {
            abort(403);
        }

        $column->delete();

        return redirect()->route('boards.show', $column->board->id)
            ->with('success', 'Column deleted successfully.');
    }

}
