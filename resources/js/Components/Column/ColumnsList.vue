<template>
    <div :class="containerClasses">
        <ColumnComponent
            v-for="column in columns"
            :key="column.id"
            :column="column"
        />
        <AddColumn :boardId="boardId" />
    </div>
</template>

<script lang="ts" setup>
import { computed } from "vue";
import ColumnComponent from "./Column.vue";
import AddColumn from "./AddColumn.vue";
import { useColumnStore } from "@/stores/Column/columnStore";

const columnStore = useColumnStore();

const columns = computed(() => columnStore.columns);

const props = defineProps<{
    boardId: number;
}>();

const boardId = props.boardId;

const containerClasses = computed(() => {
    return columns.value.length <= 2
        ? "max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex space-x-6 overflow-x-auto"
        : "w-full px-4 flex space-x-6 overflow-x-auto";
});
</script>
