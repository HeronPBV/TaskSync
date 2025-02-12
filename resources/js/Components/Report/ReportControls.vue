<script setup lang="ts">
import { useReportStore } from "@/stores/Report/reportStore";
import { useToast } from "primevue/usetoast";

const reportStore = useReportStore();
const toast = useToast();

const handleGenerateReport = async () => {
    try {
        await reportStore.fetchReport();

        const currentGeneratedAt: string =
            reportStore.report?.generated_at ?? "";

        await reportStore.generateReport();

        toast.add({
            severity: "success",
            summary: "Report",
            detail: "Report generation initiated! Please wait.",
            life: 3000,
        });

        const updatedReport = await reportStore.waitForNewReport(
            currentGeneratedAt,
            2000,
            10
        );

        if (updatedReport) {
            console.log("Final updated report:", updatedReport);
        } else {
            console.warn("Report polling reached max attempts without update.");
            toast.add({
                severity: "warn",
                summary: "Report",
                detail: "No new report found after polling.",
                life: 3000,
            });
        }
    } catch (error: any) {
        console.error("Error during report update polling:", error);
        toast.add({
            severity: "error",
            summary: "Report",
            detail: error.message,
            life: 3000,
        });
    }
};
</script>

<template>
    <button
        @click="handleGenerateReport"
        class="bg-blue-300 text-blue-600 px-4 py-2 rounded mb-6 hover:bg-blue-400 transition font-bold"
    >
        Generate New Report
    </button>
</template>
