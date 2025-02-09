<template>
    <div
        class="bg-white min-w-[300px] p-4 mb-4 rounded shadow hover:shadow-md transition duration-200 cursor-pointer"
    >
        <h4 class="font-semibold text-lg">{{ task.title }}</h4>
        <p class="text-sm text-gray-600" v-if="task.description">
            {{ task.description }}
        </p>
        <div
            class="mt-2 text-sm text-gray-500 bg-blue-50 p-2 border-t border-t-gray"
        >
            <span v-if="task.due_date" class="block">
                <strong>Due:</strong> {{ formattedDueDate }}
            </span>
            <span v-if="task.priority" class="block">
                <strong>Priority:</strong> {{ task.priority }}
            </span>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { defineProps, computed } from "vue";
import type { Task } from "@/interfaces/Task/Task";

const props = defineProps<{
    task: Task;
}>();

const formattedDueDate = computed(() => {
    if (props.task.due_date) {
        const date = new Date(props.task.due_date);
        return date.toLocaleDateString(undefined, {
            year: "numeric",
            month: "short",
            day: "numeric",
        });
    }
    return "";
});
</script>

<style scoped></style>
