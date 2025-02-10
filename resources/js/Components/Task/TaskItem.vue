<template>
    <div class="relative">
        <div v-if="isEditing">
            <TaskEditForm
                :task="task"
                @task-updated="handleTaskUpdated"
                @cancel-edit="toggleEdit"
            />
        </div>
        <div
            v-else
            class="bg-white min-w-[300px] p-4 mb-4 rounded shadow hover:shadow-md transition duration-200"
            @dblclick="toggleEdit"
        >
            <button
                @click.stop="handleDelete"
                class="absolute -top-1 -left-1 bg-red-300 text-red-600 p-2 rounded-full shadow w-5 h-5 hover:bg-red-400 flex justify-center items-center text-xl"
                title="Delete task"
            >
                &times;
            </button>
            <h4 class="font-semibold text-lg">{{ task.title }}</h4>
            <p class="text-sm text-gray-600" v-if="task.description">
                {{ task.description }}
            </p>
            <div
                v-if="task.due_date || task.priority"
                class="mt-2 text-sm text-gray-500 bg-blue-50 p-2 border-t border-t-gray-300"
            >
                <span v-if="task.due_date" class="block">
                    <strong>Due:</strong> {{ formattedDueDate }}
                </span>
                <span v-if="task.priority" class="block">
                    <strong>Priority:</strong> {{ task.priority }}
                </span>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { defineProps, ref, computed, defineEmits } from "vue";
import type { Task } from "@/interfaces/Task/Task";
import { useTaskStore } from "@/stores/Task/taskStore";
import TaskEditForm from "@/Components/Task/TaskEditForm.vue";

const props = defineProps<{ task: Task }>();
const emit = defineEmits<{ (e: "task-deleted", taskId: number): void }>();

const taskStore = useTaskStore();
const isEditing = ref(false);

const toggleEdit = () => {
    isEditing.value = !isEditing.value;
};

const handleTaskUpdated = (updatedTask: Task) => {
    isEditing.value = false;
};

const formattedDueDate = computed(() => {
    if (props.task.due_date) {
        const date = new Date(props.task.due_date);
        return date.toLocaleDateString(undefined, {
            year: "numeric",
            month: "short",
            day: "numeric",
        });
    }
    return "";
});

const handleDelete = async () => {
    if (
        confirm(
            "Are you sure you want to delete this task? This action cannot be undone."
        )
    ) {
        try {
            await taskStore.deleteTask({
                columnId: props.task.column_id,
                taskId: props.task.id,
            });
            emit("task-deleted", props.task.id);
        } catch (error) {
            console.error("Error deleting task:", error);
        }
    }
};
</script>
