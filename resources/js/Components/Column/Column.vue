<template>
    <div class="min-w-[250px] bg-gray-100 p-4 rounded shadow">
        <h3 class="text-lg font-bold mb-4">{{ column.name }}</h3>
        <div v-if="column.tasks && column.tasks.length">
            <TaskItem
                v-for="task in column.tasks"
                :key="task.id"
                :task="task"
            />
        </div>
        <div v-else class="text-gray-500 text-sm">No tasks yet.</div>
    </div>
</template>

<script lang="ts" setup>
import { defineProps } from "vue";
import TaskItem from "../Task/TaskItem.vue";
import type { Column } from "@/interfaces/Column/Column";

const props = defineProps<{
    column: Column;
}>();
</script>

<style scoped></style>
