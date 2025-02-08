<template>
    <div
        class="bg-white p-3 mb-3 rounded shadow hover:shadow-md transition duration-200 cursor-pointer"
    >
        <h4 class="font-semibold">{{ task.title }}</h4>
        <p class="text-sm text-gray-600" v-if="task.description">
            {{ task.description }}
        </p>
    </div>
</template>

<script lang="ts" setup>
import { defineProps } from "vue";
import type { Task } from "@/interfaces/Task/Task";

const props = defineProps<{
    task: Task;
}>();
</script>

<style scoped></style>
