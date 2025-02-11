<?php

use App\Models\User;
use App\Models\Board;
use App\Models\Column;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class)->in(__DIR__);

/*
|--------------------------------------------------------------------------
| End-to-End Workflow Tests
|--------------------------------------------------------------------------
|
| This test simulates a complete user workflow using the board, column, and task endpoints.
| The flow includes:
|  1. Creating a new board and verifying its presence.
|  2. Adding a column to the board.
|  3. Adding tasks to the column, including verifying that the task positions are managed correctly.
|  4. Updating a task and reordering tasks via drag and drop.
|  5. Deleting a task, the column, and finally the board.
|
*/

it('simulates a full user workflow from board creation to deletion', function () {
    // Step 1: Create a user and authenticate
    $user = User::factory()->create();
    $this->actingAs($user);

    // Create a new board
    $boardData = [
        'name' => 'Workflow Board',
        'description' => 'Board for E2E workflow test',
    ];
    $boardResponse = $this->postJson(route('boards.store'), $boardData);
    $boardResponse->assertStatus(200);
    $board = $boardResponse->json('board');
    expect($board['name'])->toBe('Workflow Board');
    expect($board['description'])->toBe('Board for E2E workflow test');

    // Step 2: Add a column to the board
    $columnData = ['name' => 'To Do'];
    $columnResponse = $this->postJson(route('boards.columns.store', ['board' => $board['id']]), $columnData);
    $columnResponse->assertStatus(200);
    $column = $columnResponse->json('column');
    expect($column['name'])->toBe('To Do');
    expect($column['board_id'])->toBe($board['id']);

    // Step 3: Add a task to the column
    $taskData = [
        'title' => 'First Task',
        'description' => 'Task description for the first task',
        'due_date' => now()->toDateString(),
        'priority' => 'high',
    ];
    $taskResponse1 = $this->postJson(route('columns.tasks.store', ['column' => $column['id']]), $taskData);
    $taskResponse1->assertStatus(200);
    $task1 = $taskResponse1->json('task');
    expect($task1['title'])->toBe('First Task');
    // According to our TaskService, the newly created task gets position 1.
    expect($task1['position'])->toBe(1);

    // Step 4: Add a second task, which should bump the existing task's position (if using increment logic)
    $taskData2 = [
        'title' => 'Second Task',
        'description' => 'Task description for the second task',
        'due_date' => now()->addDay()->toDateString(),
        'priority' => 'medium',
    ];
    $taskResponse2 = $this->postJson(route('columns.tasks.store', ['column' => $column['id']]), $taskData2);
    $taskResponse2->assertStatus(200);
    $task2 = $taskResponse2->json('task');
    // According to our business logic, the new task is created with position 1 and existing tasks are incremented.
    expect($task2['position'])->toBe(1);
    $existingTask = Task::find($task1['id']);
    expect($existingTask->position)->toBe(2);

    // Step 5: Update the second task
    $updateTaskData = [
        'title' => 'Second Task Updated',
        'description' => 'Updated description',
        'due_date' => now()->addDays(2)->toDateString(),
        'priority' => 'low',
    ];
    $updateResponse = $this->patchJson(route('tasks.update', ['task' => $task2['id']]), $updateTaskData);
    $updateResponse->assertStatus(200);
    $updatedTask2 = $updateResponse->json('task');
    expect($updatedTask2['title'])->toBe('Second Task Updated');

    // Step 6: Reorder tasks: swap positions between task1 and task2
    $reorderPayload = [
        'tasks' => [
            ['id' => $task1['id'], 'position' => 1, 'column_id' => $column['id']],
            ['id' => $task2['id'], 'position' => 2, 'column_id' => $column['id']],
        ]
    ];
    $reorderResponse = $this->patchJson(route('tasks.reorder', ['column' => $column['id']]), $reorderPayload);
    $reorderResponse->assertStatus(200);
    $updatedTask1 = Task::find($task1['id']);
    $updatedTask2 = Task::find($task2['id']);
    expect($updatedTask1->position)->toBe(1);
    expect($updatedTask2->position)->toBe(2);

    // Step 7: Delete the first task
    $deleteTaskResponse = $this->deleteJson(route('tasks.destroy', ['task' => $task1['id']]));
    $deleteTaskResponse->assertStatus(200);
    expect(Task::find($task1['id']))->toBeNull();

    // Step 8: Update the column name
    $updatedColumnData = ['name' => 'In Progress'];
    $updateColumnResponse = $this->patchJson(route('columns.update', ['column' => $column['id']]), $updatedColumnData);
    $updateColumnResponse->assertStatus(200);
    $updatedColumn = $updateColumnResponse->json('column');
    expect($updatedColumn['name'])->toBe('In Progress');

    // Step 9: Delete the column
    $deleteColumnResponse = $this->deleteJson(route('columns.destroy', ['column' => $column['id']]));
    $deleteColumnResponse->assertStatus(200);
    expect(Column::find($column['id']))->toBeNull();

    // Step 10: Finally, delete the board
    $deleteBoardResponse = $this->deleteJson(route('boards.destroy', ['board' => $board['id']]));
    $deleteBoardResponse->assertStatus(200);
    expect(Board::find($board['id']))->toBeNull();
});
