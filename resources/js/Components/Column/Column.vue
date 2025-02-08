<template>
    <div class="min-w-[350px] bg-gray-100 p-4 rounded shadow">
        <h3 class="text-lg font-bold mb-4">{{ column.name }}</h3>

        <draggable
            v-model="localTasks"
            group="tasks"
            class="flex flex-col space-y-4 min-h-[150px]"
            :animation="200"
            @change="handleChange"
        >
            <template #item="{ element }">
                <TaskItem :task="element" />
            </template>
            <template #placeholder>
                <div
                    class="text-gray-500 text-sm py-4 text-center border-dashed border-2 border-gray-300"
                >
                    Drop tasks here...
                </div>
            </template>
        </draggable>
    </div>
</template>

<script lang="ts" setup>
import { defineProps, ref, watch } from "vue";
import draggable from "vuedraggable";
import TaskItem from "../Task/TaskItem.vue";
import type { Column } from "@/interfaces/Column/Column";
import type { Task } from "@/interfaces/Task/Task";
import { useTaskStore } from "@/stores/Task/taskStore";

const props = defineProps<{
    column: Column;
}>();

const localTasks = ref<Task[]>([...props.column.tasks]);

watch(
    () => props.column.tasks,
    (newTasks) => {
        localTasks.value = [...newTasks];
    }
);

const taskStore = useTaskStore();

const updateTasksOrderForColumn = () => {
    localTasks.value.forEach((task, index) => {
        task.position = index + 1;
        task.column_id = props.column.id;
    });
    taskStore.updateTasksOrder({
        columnId: props.column.id,
        tasks: localTasks.value,
    });
};

const handleChange = (evt: any) => {
    if (evt.added) {
        evt.added.element.column_id = props.column.id;
    }
    updateTasksOrderForColumn();
};
</script>

<style scoped></style>
