import { defineStore } from "pinia";
import axios from "axios";
import { route } from "ziggy-js";
import type { Task } from "@/interfaces/Task/Task";

export const useTaskStore = defineStore("taskStore", {
    state: () => ({}),
    actions: {
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
            } catch (error) {
                console.error("Error updating tasks order:", error);
            }
        },

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
                return response.data.task;
            } catch (error) {
                console.error("Error adding task:", error);
                throw error;
            }
        },

        async deleteTask(taskId: number): Promise<void> {
            try {
                const url = route("tasks.destroy", { task: taskId });
                console.log("Deleting task via URL:", url);
                await axios.delete(url);
            } catch (error) {
                console.error("Error deleting task:", error);
                throw error;
            }
        },
    },
});
