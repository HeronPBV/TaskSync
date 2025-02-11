<template>
    <div
        class="bg-white p-6 rounded shadow mb-4 border border-dashed border-gray-300"
    >
        <form @submit.prevent="handleSubmit">
            <div class="mb-4">
                <label
                    for="name"
                    class="block text-sm font-medium text-gray-700"
                    >Title</label
                >
                <input
                    id="name"
                    type="text"
                    v-model="form.name"
                    placeholder="Board Title"
                    required
                    class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                />
            </div>
            <div class="mb-4">
                <label
                    for="description"
                    class="block text-sm font-medium text-gray-700"
                    >Description</label
                >
                <textarea
                    id="description"
                    v-model="form.description"
                    placeholder="Board Description"
                    rows="3"
                    class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                ></textarea>
            </div>
            <div v-if="error" class="text-red-500 text-sm mb-2">
                {{ error }}
            </div>
            <div class="flex justify-end space-x-2">
                <button
                    type="submit"
                    class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600"
                >
                    Save Changes
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
import { ref, defineProps, defineEmits, onMounted } from "vue";
import { useBoardStore } from "@/stores/Board/boardStore";
import type { Board } from "@/interfaces/Board/Board";

const props = defineProps<{ board: Board }>();
const emit = defineEmits<{
    (e: "board-updated", updatedBoard: Board): void;
    (e: "cancel-edit"): void;
}>();

const boardStore = useBoardStore();

const form = ref({
    name: "",
    description: "",
});
const error = ref<string | null>(null);

onMounted(() => {
    form.value.name = props.board.name;
    form.value.description = props.board.description || "";
});

const handleSubmit = async () => {
    if (!form.value.name.trim()) {
        error.value = "Title is required.";
        return;
    }
    error.value = null;
    try {
        const updatedBoard = await boardStore.updateBoard(props.board.id, {
            name: form.value.name,
            description: form.value.description,
        });
        emit("board-updated", updatedBoard);
    } catch (err) {
        error.value = "Error updating board.";
        console.error(err);
    }
};

const cancelEdit = () => {
    emit("cancel-edit");
};
</script>
