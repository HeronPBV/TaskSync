<template>
    <div
        class="flex flex-col h-full border rounded-lg p-6 shadow hover:shadow-xl transition duration-200"
    >
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
import { route } from "ziggy-js";
import { Link } from "@inertiajs/vue3";
import { defineProps } from "vue";
import type { Board } from "@/interfaces/Board/Board";

const props = defineProps<{
    board: Board;
}>();
</script>

<style scoped></style>
