<!-- resources/js/Pages/Boards/Show.vue -->
<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ board.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="mb-6">
                    <p class="text-gray-600" v-if="board.description">
                        {{ board.description }}
                    </p>
                </div>

                <ColumnsList :columns="board.columns" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script lang="ts" setup>
import { defineProps } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import ColumnsList from "@/Components/Column/ColumnsList.vue";
import type { BoardWithDetails } from "@/interfaces/Board/BoardWithDetails";

const props = defineProps<{
    board: BoardWithDetails;
}>();
</script>

<style scoped></style>
