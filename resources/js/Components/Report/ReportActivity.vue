<template>
    <div class="bg-white shadow p-6 rounded border border-gray-300">
        <h2 class="text-lg font-semibold mb-2 border-b border-black pb-2">
            Activity Today
        </h2>
        <div class="space-y-2">
            <div class="p-3 bg-gray-100 rounded shadow">
                <strong>Tasks Created:</strong> {{ report.tasks_created_today }}
            </div>
            <div class="p-3 bg-gray-100 rounded shadow">
                <strong>Tasks Deleted:</strong> {{ report.tasks_deleted_today }}
            </div>
            <div class="p-3 bg-gray-100 rounded shadow">
                <strong>Columns Created:</strong>
                {{ report.columns_created_today }}
            </div>
            <div class="p-3 bg-gray-100 rounded shadow">
                <strong>Columns Deleted:</strong>
                {{ report.columns_deleted_today }}
            </div>
            <div class="p-3 bg-gray-100 rounded shadow">
                <strong>Boards Created:</strong>
                {{ report.boards_created_today }}
            </div>
            <div class="p-3 bg-gray-100 rounded shadow">
                <strong>Boards Deleted:</strong>
                {{ report.boards_deleted_today }}
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({ report: Object });
</script>
