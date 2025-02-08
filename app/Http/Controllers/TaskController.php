<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Column;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskController extends Controller
{
    public function index(Column $column)
    {
        if ($column->board->user_id !== auth()->id()) {
            abort(403);
        }

        $tasks = $column->tasks()->get();

        return Inertia::render('Tasks/Index', [
            'column' => $column,
            'tasks' => $tasks,
        ]);
    }

    public function create(Column $column)
    {
        if ($column->board->user_id !== auth()->id()) {
            abort(403);
        }

        return Inertia::render('Tasks/Create', [
            'column' => $column,
        ]);
    }

    public function store(Request $request, Column $column)
    {
        if ($column->board->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => 'nullable|string',
            'position' => 'nullable|integer',
        ]);

        $task = $column->tasks()->create([
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'priority' => $request->priority,
            'position' => $request->position ?? 0,
        ]);

        return redirect()->route('boards.show', $column->board->id)
            ->with('success', 'Task created successfully.');
    }

    public function edit(Task $task)
    {
        if ($task->column->board->user_id !== auth()->id()) {
            abort(403);
        }

        return Inertia::render('Tasks/Edit', [
            'task' => $task,
        ]);
    }

    public function update(Request $request, Task $task)
    {
        if ($task->column->board->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => 'nullable|string',
            'position' => 'nullable|integer',
        ]);

        $task->update([
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'priority' => $request->priority,
            'position' => $request->position ?? $task->position,
        ]);

        return redirect()->route('boards.show', $task->column->board->id)
            ->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task)
    {
        if ($task->column->board->user_id !== auth()->id()) {
            abort(403);
        }

        $task->delete();

        return redirect()->route('boards.show', $task->column->board->id)
            ->with('success', 'Task deleted successfully.');
    }
}
