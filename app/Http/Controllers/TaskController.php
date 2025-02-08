<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Inertia\Inertia;
use App\Models\Column;
use Illuminate\Http\Request;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Requests\UpdateTasksOrderRequest;

class TaskController extends Controller
{
    protected TaskService $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function index(Column $column)
    {
        $this->authorize('view', $column);

        $tasks = $this->taskService->getTasksForColumn($column);

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
        $task = $this->taskService->createTask($column, $request->validated());

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
        $this->taskService->updateTask($task, $request->validated());

        return redirect()->route('boards.show', $task->column->board->id)
            ->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);

        $this->taskService->deleteTask($task);

        return redirect()->route('boards.show', $task->column->board->id)
            ->with('success', 'Task deleted successfully.');
    }

    public function reorder(UpdateTasksOrderRequest $request, Column $column): JsonResponse
    {
        $tasksData = $request->validated()['tasks'];

        $success = $this->taskService->reorderTasks($column, $tasksData);

        if ($success) {
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 500);
    }



}
