// resources/js/stores/Task/taskStore.ts
import { defineStore } from "pinia";
import axios from "axios";
import { route } from "ziggy-js";
import type { Task } from "@/interfaces/Task/Task";
import { socketService } from "@/services/socketService";

/**
 * Store for managing tasks.
 *
 * Tasks are stored in an object, grouped by the column ID.
 * This enables centralized access and updates for tasks in each column.
 */
export const useTaskStore = defineStore("taskStore", {
    state: () => ({
        // tasksByColumn stores tasks for each column, using the column ID as the key.
        tasksByColumn: {} as Record<number, Task[]>,
        loading: false,
        error: null as string | null,
    }),
    actions: {
        /**
         * Initializes the tasks for a given column.
         *
         * @param columnId - The ID of the column.
         * @param tasks - An array of tasks for that column.
         *
         * @example
         * taskStore.setTasksForColumn(1, tasksArray);
         */
        setTasksForColumn(columnId: number, tasks: Task[]) {
            this.tasksByColumn[columnId] = tasks;
        },

        /**
         * Updates the order of tasks within a column.
         *
         * Prepares a payload with the updated tasks (including `id`, `position`, and `column_id`)
         * and sends a PATCH request to the endpoint responsible for reordering.
         * On success, updates the tasks state in the store.
         *
         * @param columnId - The ID of the column.
         * @param tasks - An array of tasks with the new positions.
         *
         * @example
         * await taskStore.updateTasksOrder({ columnId: 1, tasks: updatedTasks });
         */
        async updateTasksOrder({
            columnId,
            tasks,
        }: {
            columnId: number;
            tasks: Task[];
        }) {
            const tasksPayload = tasks.map((task) => ({
                id: task.id,
                position: task.position,
                column_id: task.column_id,
            }));
            try {
                await axios.patch(route("tasks.reorder", columnId), {
                    tasks: tasksPayload,
                });
                this.tasksByColumn[columnId] = tasks;
            } catch (error) {
                console.error("Error updating tasks order:", error);
            }
        },

        /**
         * Creates a new task in a specific column.
         *
         * Sends a POST request to the endpoint for task creation, ensuring that the new task
         * is created with `position` 1. After creation, the new task is inserted at the beginning
         * of the tasks array for that column in the store.
         *
         * @param columnId - The ID of the column in which the task will be created.
         * @param data - A partial object containing the task data (e.g., title, description, etc.).
         * @returns A Promise that resolves with the newly created task.
         *
         * @example
         * const newTask = await taskStore.addTask({ columnId: 1, data: { title: "New Task" } });
         */
        async addTask({
            columnId,
            data,
        }: {
            columnId: number;
            data: Partial<Task>;
        }): Promise<Task> {
            try {
                const response = await axios.post(
                    route("columns.tasks.store", { column: columnId }),
                    {
                        ...data,
                        position: 1,
                    }
                );
                const newTask = response.data.task as Task;
                if (!this.tasksByColumn[columnId]) {
                    this.tasksByColumn[columnId] = [];
                }
                this.tasksByColumn[columnId].unshift(newTask);
                return newTask;
            } catch (error) {
                console.error("Error adding task:", error);
                throw error;
            }
        },

        /**
         * Deletes a task.
         *
         * Sends a DELETE request to the endpoint for task deletion.
         * Afterwards, removes the task from the tasks array for that column in the store.
         *
         * @param columnId - The ID of the column to which the task belongs.
         * @param taskId - The ID of the task to be deleted.
         *
         * @example
         * await taskStore.deleteTask({ columnId: 1, taskId: 123 });
         */
        async deleteTask({
            columnId,
            taskId,
        }: {
            columnId: number;
            taskId: number;
        }): Promise<void> {
            try {
                await axios.delete(route("tasks.destroy", { task: taskId }));
                if (this.tasksByColumn[columnId]) {
                    this.tasksByColumn[columnId] = this.tasksByColumn[
                        columnId
                    ].filter((task) => task.id !== taskId);
                }
            } catch (error) {
                console.error("Error deleting task:", error);
                throw error;
            }
        },

        /**
         * Updates an existing task.
         *
         * Sends a PATCH request with the updated task data and updates the task in the store.
         *
         * @param task - The task to update.
         * @param data - Partial task data with the updates (e.g., title, description, due_date, priority).
         * @returns A Promise that resolves with the updated task.
         *
         * @example
         * const updatedTask = await taskStore.updateTask(task, { title: "New Title" });
         */
        async updateTask(task: Task, data: Partial<Task>): Promise<Task> {
            try {
                const response = await axios.patch(
                    route("tasks.update", { task: task.id }),
                    data
                );
                const updatedTask = response.data.task as Task;
                if (this.tasksByColumn[task.column_id]) {
                    this.tasksByColumn[task.column_id] = this.tasksByColumn[
                        task.column_id
                    ].map((t) => (t.id === task.id ? updatedTask : t));
                }
                return updatedTask;
            } catch (error) {
                console.error("Error updating task:", error);
                throw error;
            }
        },

        registerWebSocketEvents() {
            socketService.on("TaskCreated", (data) => {
                this.tasksByColumn[data.task.column_id].push(data.task);
            });

            socketService.on("TaskUpdated", (data) => {
                const tasks = this.tasksByColumn[data.task.column_id] || [];
                const index = tasks.findIndex((t) => t.id === data.task.id);
                if (index !== -1) {
                    tasks[index] = data.task;
                }
            });

            socketService.on("TaskDeleted", (data) => {
                this.tasksByColumn[data.task.column_id] = this.tasksByColumn[
                    data.task.column_id
                ].filter((t) => t.id !== data.task.id);
            });
        },
    },
});
