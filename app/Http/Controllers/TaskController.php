<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Models\Column;
use Inertia\Inertia;

class TaskController extends Controller
{
    public function index(Column $column)
    {
        $this->authorize('view', $column);

        $tasks = $column->tasks()->get();

        return Inertia::render('Tasks/Index', [
            'column' => $column,
            'tasks' => $tasks,
        ]);
    }


    public function create(Column $column)
    {
        $this->authorize('create', [Task::class, $column]);

        return Inertia::render('Tasks/Create', [
            'column' => $column,
        ]);
    }


    public function store(StoreTaskRequest $request, Column $column)
    {
        $task = $column->tasks()->create($request->validated());

        return redirect()->route('boards.show', $column->board->id)
            ->with('success', 'Task created successfully.');
    }

    public function edit(Task $task)
    {
        $this->authorize('update', $task);

        return Inertia::render('Tasks/Edit', [
            'task' => $task,
        ]);
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        $task->update($request->validated());

        return redirect()->route('boards.show', $task->column->board->id)
            ->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);
        $task->delete();

        return redirect()->route('boards.show', $task->column->board->id)
            ->with('success', 'Task deleted successfully.');
    }
}
