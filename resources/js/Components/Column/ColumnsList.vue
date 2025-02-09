<template>
    <div :class="containerClasses">
        <ColumnComponent
            v-for="column in localColumns"
            :key="column.id"
            :column="column"
        />
        <AddColumn :boardId="boardId" @column-added="handleColumnAdded" />
    </div>
</template>

<script lang="ts" setup>
import { defineProps, ref, computed } from "vue";
import ColumnComponent from "./Column.vue";
import AddColumn from "./AddColumn.vue";
import type { Column } from "@/interfaces/Column/Column";

const props = defineProps<{
    columns: Column[];
    boardId: number;
}>();

const localColumns = ref<Column[]>([...props.columns]);

import { watch } from "vue";
watch(
    () => props.columns,
    (newColumns) => {
        localColumns.value = [...newColumns];
    }
);

const containerClasses = computed(() => {
    return localColumns.value.length <= 2
        ? "max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex space-x-6 overflow-x-auto"
        : "w-full px-4 flex space-x-6 overflow-x-auto";
});

function handleColumnAdded(newColumn: Column) {
    console.log("Novo item recebido:", newColumn);
    if (newColumn && newColumn.id) {
        localColumns.value = [...localColumns.value, newColumn];
        console.log("localColumns atualizado:", localColumns.value);
    } else {
        console.warn("Nova coluna sem ID válido", newColumn);
    }
}

const boardId = props.boardId;
</script>
