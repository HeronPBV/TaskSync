<template>
    <Head :title="title" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ board.name }}
            </h2>
        </template>

        <p
            class="text-gray-600 max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8"
            v-if="board.description"
        >
            {{ board.description }}
        </p>
        <p class="text-gray-500 text-xs italic mx-auto mb-2 w-fit">
            You can drag and drop the task cards.
        </p>
        <div class="mx-auto sm:px-6 lg:px-8 py-6 bg-blue-300">
            <ColumnsList :boardId="board.id" />
        </div>
    </AuthenticatedLayout>
</template>

<script lang="ts" setup>
import { defineProps, onMounted } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import ColumnsList from "@/Components/Column/ColumnsList.vue";
import type { BoardWithDetails } from "@/interfaces/Board/BoardWithDetails";
import { useColumnStore } from "@/stores/Column/columnStore";

const props = defineProps<{
    board: BoardWithDetails;
}>();
const title = "Board " + props.board.id;

const columnStore = useColumnStore();
onMounted(() => {
    if (props.board.columns) {
        columnStore.setColumns(props.board.columns);
    }
});
</script>
