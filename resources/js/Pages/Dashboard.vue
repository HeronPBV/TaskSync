<template>
    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6"
                >
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-2xl font-semibold">Your Boards</h2>
                        <button
                            @click="showForm = true"
                            class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
                        >
                            Add Board
                        </button>
                    </div>

                    <BoardAddForm
                        v-if="showForm"
                        @board-added="handleBoardAdded"
                        @cancel-add="showForm = false"
                    />

                    <BoardList :boards="boards" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script lang="ts" setup>
import { Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import BoardList from "@/Components/Board/BoardList.vue";
import BoardAddForm from "@/Components/Board/BoardAddForm.vue";
import { ref, onMounted, computed } from "vue";
import { useBoardStore } from "@/stores/Board/boardStore";
import type { Board } from "@/interfaces/Board/Board";

const props = defineProps<{
    boards: Board[];
}>();

const boards = computed(() => boardStore.boards);

const boardStore = useBoardStore();

const showForm = ref(false);

const handleBoardAdded = (newBoard: Board) => {
    showForm.value = false;
};

onMounted(() => {
    boardStore.boards = [...props.boards];
});
</script>
