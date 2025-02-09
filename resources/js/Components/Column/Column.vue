<template>
    <div class="min-w-[350px] bg-gray-100 p-4 rounded shadow">
        <h3 class="text-lg font-bold mb-4">{{ column.name }}</h3>

        <div v-if="!hasTasks" class="text-gray-500 text-sm py-4 text-center">
            No tasks yet.
        </div>

        <draggable
            v-model="localTasks"
            group="tasks"
            class="flex flex-col space-y-4 min-h-[150px] border-b border-dashed border-gray-300 p-2"
            :animation="200"
            @change="handleChange"
            @end="handleEnd"
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
        <div v-if="!hasTasks" class="mt-4">
            <button
                @click="deleteColumn"
                class="text-red-600 hover:text-red-800 text-sm font-bold bg-red-200 p-3 rounded shadow w-full"
            >
                Delete Column
            </button>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { defineProps, ref, watch, computed } from "vue";
import draggable from "vuedraggable";
import TaskItem from "../Task/TaskItem.vue";
import type { Column } from "@/interfaces/Column/Column";
import type { Task } from "@/interfaces/Task/Task";
import { useTaskStore } from "@/stores/Task/taskStore";
import { useColumnStore } from "@/stores/Column/columnStore";
import { Inertia } from "@inertiajs/inertia";
import { route } from "ziggy-js";

const props = defineProps<{
    column: Column;
}>();

const localTasks = ref<Task[]>([...(props.column.tasks ?? [])]);

watch(
    () => props.column.tasks,
    (newTasks) => {
        localTasks.value = [...(newTasks ?? [])];
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

const handleEnd = () => {
    updateTasksOrderForColumn();
};

const hasTasks = computed(() => localTasks.value.length > 0);

const columnStore = useColumnStore();

function deleteColumn() {
    if (
        confirm(
            "Are you sure you want to delete this column? This action cannot be undone."
        )
    ) {
        columnStore.deleteColumn(props.column.id);
    }
}
</script>
