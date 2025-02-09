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
                console.error("Error can't update tasks order:", error);
            }
        },
    },
});
