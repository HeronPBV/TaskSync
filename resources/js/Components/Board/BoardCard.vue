<template>
    <div
        class="relative flex flex-col h-full border rounded-lg p-6 shadow hover:shadow-xl transition duration-200"
    >
        <button
            @click="handleDelete"
            class="absolute -top-1 -left-1 bg-red-300 text-red-600 p-2 rounded-full shadow w-5 h-5 hover:bg-red-400 flex justify-center items-center text-xl"
            title="Delete Board"
        >
            &times;
        </button>
        <div>
            <h2 class="text-xl font-semibold mb-2">{{ board.name }}</h2>
            <p class="text-gray-600" v-if="board.description">
                {{ board.description }}
            </p>
        </div>
        <div class="mt-auto pt-4">
            <Link
                :href="route('boards.show', board.id)"
                class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-200 cursor-pointer"
            >
                Open Board
            </Link>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { defineProps } from "vue";
import { route } from "ziggy-js";
import { Link } from "@inertiajs/vue3";
import { useBoardStore } from "@/stores/Board/boardStore";
import type { Board } from "@/interfaces/Board/Board";

const props = defineProps<{ board: Board }>();

const boardStore = useBoardStore();

const handleDelete = async () => {
    if (
        confirm(
            "Are you sure you want to delete this board? All data associated with this board will be permanently deleted."
        )
    ) {
        try {
            await boardStore.deleteBoard(props.board.id);
        } catch (error) {
            console.error("Error deleting board:", error);
        }
    }
};
</script>
