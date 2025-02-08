<template>
    <div class="flex space-x-6 overflow-x-auto" :class="containerClasses">
        <ColumnComponent
            v-for="column in columns"
            :key="column.id"
            :column="column"
        />
    </div>
</template>

<script lang="ts" setup>
import { defineProps, computed } from "vue";
import ColumnComponent from "./Column.vue";
import type { Column } from "@/interfaces/Column/Column";

const props = defineProps<{
    columns: Column[];
}>();

const containerClasses = computed(() => {
    if (props.columns.length <= 3) {
        return "max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8";
    } else {
        return "w-full px-4";
    }
});
</script>

<style scoped></style>
