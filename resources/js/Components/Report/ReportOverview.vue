<template>
    <div class="bg-white shadow p-6 rounded border border-gray-300">
        <h2 class="text-lg font-semibold mb-2 border-b border-black pb-2">
            General Statistics
        </h2>
        <div class="space-y-2">
            <div class="p-3 bg-gray-100 rounded shadow">
                <strong>Total Boards:</strong> {{ report.total_boards }}
            </div>
            <div class="p-3 bg-gray-100 rounded shadow">
                <strong>Total Columns:</strong> {{ report.total_columns }}
            </div>
            <div class="p-3 bg-gray-100 rounded shadow">
                <strong>Total Tasks:</strong> {{ report.total_tasks }}
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({ report: Object });
</script>
