import { defineStore } from "pinia";
import axios from "axios";
import { route } from "ziggy-js";
import type { Column } from "@/interfaces/Column/Column";

/**
 * Store for managing columns.
 */
export const useColumnStore = defineStore("columnStore", {
    state: () => ({
        columns: [] as Column[],
        loading: false,
        error: null as string | null,
    }),
    actions: {
        /**
         * Sets the columns in the store state.
         *
         * @param newColumns - An array of columns to set.
         *
         * @example
         * columnStore.setColumns(newColumnsArray);
         */
        setColumns(newColumns: Column[]) {
            this.columns = [...newColumns];
        },

        /**
         * Creates a new column for a specific board.
         *
         * @param boardId - The ID of the board to which the column belongs.
         * @param name - The name of the new column.
         * @returns A Promise that resolves with the created Column.
         *
         * @example
         * const newColumn = await columnStore.addColumn(1, "New Column");
         */
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

        /**
         * Deletes a column by its ID.
         *
         * @param columnId - The ID of the column to delete.
         * @returns A Promise that resolves when the column has been deleted.
         *
         * @example
         * await columnStore.deleteColumn(123);
         */
        async deleteColumn(columnId: number): Promise<void> {
            this.loading = true;
            this.error = null;
            try {
                await axios.delete(
                    route("columns.destroy", { column: columnId })
                );
                this.columns = this.columns.filter(
                    (col) => col.id !== columnId
                );
            } catch (error: any) {
                this.error = "Error deleting column.";
                console.error("Error deleting column:", error);
                throw error;
            } finally {
                this.loading = false;
            }
        },
    },
});
