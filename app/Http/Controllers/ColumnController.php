<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreColumnRequest;
use App\Http\Requests\UpdateColumnRequest;
use App\Models\Board;
use App\Models\Column;
use App\Services\ColumnService;
use Inertia\Inertia;

class ColumnController extends Controller
{
    protected ColumnService $columnService;

    public function __construct(ColumnService $columnService)
    {
        $this->columnService = $columnService;
    }

    public function store(StoreColumnRequest $request, Board $board)
    {
        $column = $this->columnService->createColumn($board, $request->validated());

        return response()->json(['column' => $column]);
    }

    public function update(UpdateColumnRequest $request, Column $column)
    {
        $this->columnService->updateColumn($column, $request->validated());
        return response()->json(['column' => $column]);
    }


    public function destroy(Column $column)
    {
        $this->authorize('delete', $column);

        $this->columnService->deleteColumn($column);

        return response()->json(['success' => 'Column deleted successfully.']);
    }
}
