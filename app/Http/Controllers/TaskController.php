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

    public function store(StoreTaskRequest $request, Column $column)
    {
        $task = $this->taskService->createTask($column, $request->validated());

        if (request()->wantsJson()) {
            return response()->json(['task' => $task]);
        }

        return redirect()->route('boards.show', $column->board->id)
            ->with('success', 'Task created successfully.');
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        $this->taskService->updateTask($task, $request->validated());

        return response()->json(['task' => $task]);
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);

        $boardId = $task->column->board->id;

        $this->taskService->deleteTask($task);

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Task deleted successfully.']);
        }

        return redirect()->route('boards.show', $boardId)
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
