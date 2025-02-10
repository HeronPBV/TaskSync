<template>
    <div
        class="bg-white p-4 rounded shadow border border-dashed border-gray-300"
    >
        <form @submit.prevent="handleSubmit">
            <div class="mb-2">
                <input
                    v-model="form.title"
                    type="text"
                    placeholder="Title"
                    class="w-full p-2 border rounded"
                    @keyup.enter="handleSubmit"
                    required
                    autofocus
                />
            </div>
            <div class="mb-2">
                <textarea
                    v-model="form.description"
                    placeholder="Description (optional)"
                    class="w-full p-2 border rounded"
                ></textarea>
            </div>
            <div class="mb-2">
                <input
                    v-model="form.due_date"
                    type="date"
                    class="w-full p-2 border rounded"
                />
            </div>
            <div class="mb-2">
                <select
                    v-model="form.priority"
                    class="w-full p-2 border rounded"
                >
                    <option value="">Select priority</option>
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                </select>
            </div>
            <div v-if="error" class="text-red-600 text-sm mb-2">
                {{ error }}
            </div>
            <div class="flex justify-end space-x-2">
                <button
                    type="submit"
                    class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600"
                >
                    Save
                </button>
                <button
                    type="button"
                    @click="cancelEdit"
                    class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400"
                >
                    Cancel
                </button>
            </div>
        </form>
    </div>
</template>

<script lang="ts" setup>
import { defineProps, defineEmits, ref, onMounted } from "vue";
import type { Task } from "@/interfaces/Task/Task";
import { useTaskStore } from "@/stores/Task/taskStore";

const props = defineProps<{ task: Task }>();
const emit = defineEmits<{
    (e: "task-updated", updatedTask: Task): void;
    (e: "cancel-edit"): void;
}>();

const taskStore = useTaskStore();

const form = ref({
    title: "",
    description: "",
    due_date: "",
    priority: "",
});
const error = ref<string | null>(null);

onMounted(() => {
    form.value.title = props.task.title;
    form.value.description = props.task.description || "";
    form.value.due_date = props.task.due_date || "";
    form.value.priority = props.task.priority || "";
});

const handleSubmit = async () => {
    if (!form.value.title.trim()) {
        error.value = "Title is required.";
        return;
    }
    error.value = null;
    try {
        const updatedTask = await taskStore.updateTask(props.task, form.value);
        emit("task-updated", updatedTask);
    } catch (err) {
        error.value = "Error updating task.";
        console.error(err);
    }
};

const cancelEdit = () => {
    emit("cancel-edit");
};
</script>
