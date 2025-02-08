<script lang="ts" setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import BoardList from "@/Components/Board/BoardList.vue";
import { defineProps } from "vue";

const props = defineProps<{
    boards: Array<{ id: number; name: string; description?: string }>;
}>();
</script>

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
                    <h2 class="text-2xl font-semibold mb-4">Your Boards</h2>
                    <BoardList :boards="boards" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
