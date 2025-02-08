<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreColumnRequest;
use App\Http\Requests\UpdateColumnRequest;
use App\Models\Board;
use App\Models\Column;
use Inertia\Inertia;

class ColumnController extends Controller
{
    public function index(Board $board)
    {
        $this->authorize('view', $board);

        $columns = $board->columns()->get();

        return Inertia::render('Columns/Index', [
            'board' => $board,
            'columns' => $columns,
        ]);
    }


    public function create(Board $board)
    {
        $this->authorize('create', [Column::class, $board]);

        return Inertia::render('Columns/Create', [
            'board' => $board,
        ]);
    }


    public function store(StoreColumnRequest $request, Board $board)
    {
        $column = $board->columns()->create($request->validated());

        return redirect()->route('boards.show', $board->id)
            ->with('success', 'Column created successfully.');
    }

    public function edit(Column $column)
    {
        $this->authorize('update', $column);

        return Inertia::render('Columns/Edit', [
            'column' => $column,
        ]);
    }

    public function update(UpdateColumnRequest $request, Column $column)
    {
        $column->update($request->validated());

        return redirect()->route('boards.show', $column->board->id)
            ->with('success', 'Column updated successfully.');
    }

    public function destroy(Column $column)
    {
        $this->authorize('delete', $column);
        $column->delete();

        return redirect()->route('boards.show', $column->board->id)
            ->with('success', 'Column deleted successfully.');
    }
}
