<template>
    <Head title="Activity Report" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Activity Report
            </h2>
        </template>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6"
                >
                    <ReportControls />

                    <div
                        v-if="report"
                        class="grid grid-cols-1 md:grid-cols-2 gap-4"
                    >
                        <ReportOverview :report="report" />
                        <ReportActivity :report="report" />
                    </div>

                    <div v-else class="text-red-500 text-center mt-4">
                        No report available.
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { onMounted, computed } from "vue";
import { Head } from "@inertiajs/vue3";
import { useReportStore } from "@/stores/Report/reportStore";
import ReportControls from "@/Components/Report/ReportControls.vue";
import ReportOverview from "@/Components/Report/ReportOverview.vue";
import ReportActivity from "@/Components/Report/ReportActivity.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const reportStore = useReportStore();
const report = computed(() => reportStore.report);

onMounted(() => {
    reportStore.fetchReport();
});
</script>
