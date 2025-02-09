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

                    <BoardList :boards="localBoards" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script lang="ts" setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import BoardList from "@/Components/Board/BoardList.vue";
import BoardAddForm from "@/Components/Board/BoardAddForm.vue";
import { ref } from "vue";

const props = defineProps<{
    boards: Array<{ id: number; name: string; description?: string }>;
}>();

const localBoards = ref([...props.boards]);
const showForm = ref(false);

const handleBoardAdded = (newBoard: {
    id: number;
    name: string;
    description?: string;
}) => {
    localBoards.value.push(newBoard);
    showForm.value = false;
};
</script>
