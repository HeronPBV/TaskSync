<template>
    <div class="flex space-x-6 overflow-x-auto">
        <ColumnComponent
            v-for="column in columns"
            :key="column.id"
            :column="column"
        />
    </div>
</template>

<script lang="ts" setup>
import { defineProps } from "vue";
import ColumnComponent from "./Column.vue";
import type { Column } from "@/interfaces/Column/Column";

const props = defineProps<{
    columns: Column[];
}>();
</script>

<style scoped></style>
