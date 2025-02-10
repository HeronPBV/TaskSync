<template>
    <div class="min-w-[350px] bg-gray-100 p-4 rounded shadow">
        <div class="flex justify-between items-center">
            <div class="flex-1">
                <template v-if="isEditing">
                    <input
                        v-model="editedName"
                        @keyup.enter="saveEdit"
                        @blur="saveEdit"
                        type="text"
                        class="w-11/12 p-2 border rounded"
                        autofocus
                    />
                </template>
                <template v-else>
                    <h3
                        class="text-lg font-bold mb-4 cursor-pointer"
                        @dblclick="startEditing"
                    >
                        {{ column.name }}
                    </h3>
                </template>
            </div>
            <button
                @click="openAddTaskForm"
                class="bg-green-300 text-green-600 p-2 rounded-full shadow w-6 h-6 hover:bg-green-400 flex justify-center items-center text-2xl"
            >
                +
            </button>
        </div>

        <div v-if="!hasTasks" class="text-gray-500 text-sm py-4 text-center">
            No tasks yet.
        </div>

        <div v-if="showAddTaskForm">
            <TaskAddForm
                :columnId="columnId"
                @task-added="handleTaskAdded"
                @cancel-add="closeAddTaskForm"
            />
        </div>

        <draggable
            v-model="tasks"
            :item-key="'id'"
            group="tasks"
            class="flex flex-col space-y-4 min-h-[150px] border-b border-dashed border-gray-300 p-2"
            :animation="200"
            @change="handleChange"
            @end="handleEnd"
        >
            <template #item="{ element }">
                <TaskItem :task="element" @task-deleted="removeTask" />
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
import { defineProps, ref, computed, onMounted } from "vue";
import draggable from "vuedraggable";
import TaskItem from "../Task/TaskItem.vue";
import TaskAddForm from "../Task/TaskAddForm.vue";
import type { Column } from "@/interfaces/Column/Column";
import type { Task } from "@/interfaces/Task/Task";
import { useTaskStore } from "@/stores/Task/taskStore";
import { useColumnStore } from "@/stores/Column/columnStore";
import debounce from "lodash.debounce";
import { route } from "ziggy-js";

const props = defineProps<{ column: Column }>();
const columnId = props.column.id;

const taskStore = useTaskStore();
const columnStore = useColumnStore();

onMounted(() => {
    if (!taskStore.tasksByColumn[columnId]) {
        taskStore.setTasksForColumn(columnId, props.column.tasks ?? []);
    }
});

const tasks = computed<Task[]>({
    get() {
        return taskStore.tasksByColumn[columnId] || [];
    },
    set(newTasks) {
        taskStore.tasksByColumn[columnId] = newTasks;
        debouncedUpdateTasksOrder();
    },
});

const updateTasksOrderForColumn = () => {
    if (tasks.value.length === 0) return;
    tasks.value.forEach((task, index) => {
        task.position = index + 1;
        task.column_id = columnId;
    });
    taskStore.updateTasksOrder({ columnId, tasks: tasks.value });
};

const debouncedUpdateTasksOrder = debounce(updateTasksOrderForColumn, 300);

const handleChange = (evt: any) => {
    if (evt.added) {
        evt.added.element.column_id = columnId;
    }
    debouncedUpdateTasksOrder();
};

const handleEnd = () => {
    debouncedUpdateTasksOrder();
};

const hasTasks = computed(() => tasks.value.length > 0);

const removeTask = (taskId: number) => {
    tasks.value = tasks.value.filter((task) => task.id !== taskId);
};

const showAddTaskForm = ref(false);
const openAddTaskForm = () => {
    showAddTaskForm.value = true;
};
const closeAddTaskForm = () => {
    showAddTaskForm.value = false;
};

const handleTaskAdded = (newTask: Task) => {
    closeAddTaskForm();
};

const isEditing = ref(false);
const editedName = ref(props.column.name);

const startEditing = () => {
    isEditing.value = true;
    editedName.value = props.column.name;
};

const saveEdit = async () => {
    if (
        editedName.value.trim() === "" ||
        editedName.value === props.column.name
    ) {
        isEditing.value = false;
        return;
    }
    try {
        await columnStore.updateColumn(columnId, { name: editedName.value });
        isEditing.value = false;
    } catch (error) {
        console.error("Error updating column name:", error);
    }
};

const deleteColumn = () => {
    if (
        confirm(
            "Are you sure you want to delete this column? This action cannot be undone."
        )
    ) {
        columnStore.deleteColumn(columnId);
    }
};
</script>
