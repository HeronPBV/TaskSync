<template>
    <div
        class="bg-white min-w-[300px] p-4 mb-4 rounded shadow border border-dashed border-gray-300"
    >
        <form @submit.prevent="handleSubmit">
            <div class="mb-2">
                <input
                    v-model="form.title"
                    type="text"
                    placeholder="Title"
                    class="w-full p-2 border rounded"
                    ref="titleInput"
                    required
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
            <div class="flex justify-end">
                <button
                    type="submit"
                    class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600"
                >
                    Save
                </button>
            </div>
        </form>
    </div>
</template>

<script lang="ts" setup>
import { ref, onMounted, defineProps, defineEmits } from "vue";
import { useTaskStore } from "@/stores/Task/taskStore";
import type { Task } from "@/interfaces/Task/Task";

const props = defineProps<{ columnId: number }>();
const emit = defineEmits<{
    (e: "task-added", newTask: Task): void;
    (e: "cancel-add"): void;
}>();

const form = ref({
    title: "",
    description: "",
    due_date: "",
    priority: "",
});
const loading = ref(false);
const error = ref<string | null>(null);
const titleInput = ref<HTMLInputElement | null>(null);
const taskStore = useTaskStore();

const handleSubmit = async () => {
    if (!form.value.title.trim()) {
        error.value = "Title is required.";
        return;
    }
    error.value = null;
    loading.value = true;
    try {
        const newTask = await taskStore.addTask({
            columnId: props.columnId,
            data: { ...form.value },
        });
        emit("task-added", newTask);
        form.value = { title: "", description: "", due_date: "", priority: "" };
    } catch (err) {
        error.value = "Error adding task.";
        console.error(err);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    if (titleInput.value) {
        titleInput.value.focus();
    }
});
</script>
