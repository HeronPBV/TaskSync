<template>
    <div>
        <div
            v-if="!editing"
            class="min-w-[350px] bg-gray-100 p-4 rounded shadow flex items-center justify-center cursor-pointer hover:bg-gray-200 transition-colors"
            @click="editing = true"
        >
            <span class="text-gray-500">+ Add Column</span>
        </div>

        <div v-else class="min-w-[350px] bg-gray-100 p-4 rounded shadow">
            <input
                type="text"
                v-model="columnName"
                @keyup.enter="saveColumn"
                @blur="handleBlur"
                class="w-full p-2 border rounded"
                placeholder="Column name"
                autofocus
            />
        </div>
    </div>
</template>

<script lang="ts" setup>
import { ref } from "vue";
import { useColumnStore } from "@/stores/Column/columnStore";
const emit = defineEmits<{ (e: "column-added", newColumn: any): void }>();
const props = defineProps<{ boardId: number }>();

const editing = ref(false);
const columnName = ref("");

function cancelEdit() {
    editing.value = false;
    columnName.value = "";
}

async function saveColumn() {
    if (columnName.value.trim() === "") {
        return cancelEdit();
    }
    const columnStore = useColumnStore();
    try {
        const newColumn = await columnStore.addColumn(
            props.boardId,
            columnName.value
        );
        emit("column-added", newColumn);
        cancelEdit();
    } catch (error) {
        console.error("Error creating column", error);
        cancelEdit();
    }
}

function handleBlur() {
    if (columnName.value.trim() !== "") {
        saveColumn();
    } else {
        cancelEdit();
    }
}
</script>
