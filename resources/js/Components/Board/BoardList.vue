<template>
    <p class="text-gray-500 text-xs italic mx-auto mb-2 w-fit">
        Double click in a card to update board data
    </p>
    <div
        v-if="boards.length"
        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6"
    >
        <BoardCard v-for="board in boards" :key="board.id" :board="board" />
    </div>
    <div v-else class="text-gray-600">You don't have any boards yet.</div>
</template>

<script lang="ts" setup>
import { defineProps } from "vue";
import BoardCard from "@/Components/Board/BoardCard.vue";
import type { Board } from "@/interfaces/Board/Board";

defineProps<{ boards: Board[] }>();
</script>
