import { defineStore } from "pinia";
import axios from "axios";
import { route } from "ziggy-js";
import { Inertia } from "@inertiajs/inertia";
import type { Column } from "@/interfaces/Column/Column";

export const useColumnStore = defineStore("columnStore", {
    state: () => ({
        columns: [] as Column[],
        loading: false,
        error: null as string | null,
    }),
    actions: {
        async addColumn(boardId: number, name: string): Promise<Column> {
            this.loading = true;
            try {
                const response = await axios.post(
                    route("boards.columns.store", { board: boardId }),
                    { name }
                );
                const newColumn: Column = response.data.column;
                this.columns.push(newColumn);
                return newColumn;
            } catch (error) {
                this.error = "Error adding column";
                console.error("Error adding column:", error);
                throw error;
            } finally {
                this.loading = false;
            }
        },
        deleteColumn(columnId: number) {
            Inertia.delete(route("columns.destroy", { column: columnId }), {
                preserveState: true,
                onSuccess: () => {
                    console.log("Column deleted successfully");
                    this.columns = this.columns.filter(
                        (col) => col.id !== columnId
                    );
                },
                onError: (errors: any) => {
                    console.error("Error deleting column:", errors);
                },
            });
        },
    },
});
