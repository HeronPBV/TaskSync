import axios from "axios";
import { defineStore } from "pinia";
import type { Report } from "@/interfaces/Report/Report";

export const useReportStore = defineStore("reportStore", {
    state: () => ({
        report: null as Report | null,
        loading: false,
        error: null as string | null,
    }),
    actions: {
        async fetchReport() {
            this.loading = true;
            try {
                const response = await axios.get("/reports");
                this.report = response.data.report;
            } catch (error: any) {
                this.error = error.message || "Error fetching report";
            } finally {
                this.loading = false;
            }
        },

        async generateReport() {
            try {
                await axios.post("/reports/generate");
            } catch (error: any) {
                throw new Error("Error generating report: " + error.message);
            }
        },

        /**
         * Waits for the report to update by comparing the generated_at timestamp.
         * Uses a recursive setTimeout polling mechanism.
         *
         * @param currentGeneratedAt The generated_at value of the current report.
         * @param intervalMs The interval in milliseconds between polling attempts.
         * @param maxAttempts The maximum number of polling attempts.
         * @returns A Promise that resolves with the new Report once updated.
         */
        async waitForNewReport(
            currentGeneratedAt: string | null,
            intervalMs: number = 2000,
            maxAttempts: number = 10
        ): Promise<Report | null> {
            let attempt = 0;

            const poll = async (): Promise<Report | null> => {
                attempt++;
                console.log(`Polling attempt ${attempt}/${maxAttempts}`);

                try {
                    await this.fetchReport();

                    if (this.report?.generated_at) {
                        if (this.report.generated_at !== currentGeneratedAt) {
                            console.log("Report updated.");
                            return this.report;
                        }
                    } else {
                        console.warn("Report not available yet, retrying...");
                    }

                    if (attempt >= maxAttempts) {
                        console.warn(
                            "Polling reached max attempts without detecting an update."
                        );
                        return null;
                    }

                    return new Promise((resolve) => {
                        setTimeout(() => resolve(poll()), intervalMs);
                    });
                } catch (error) {
                    console.error("Error in polling:", error);
                    return null;
                }
            };

            return poll();
        },
    },
});
