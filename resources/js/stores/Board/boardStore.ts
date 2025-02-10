import { defineStore } from "pinia";
import axios from "axios";
import { route } from "ziggy-js";

/**
 * Interface representing a Board.
 */
export interface Board {
    id: number;
    name: string;
    description?: string;
}

/**
 * Store for managing boards.
 */
export const useBoardStore = defineStore("boardStore", {
    state: () => ({
        boards: [] as Board[],
        loading: false,
        error: null as string | null,
    }),
    actions: {
        /**
         * Creates a new board.
         *
         * @param data - An object containing the board data. Must include 'name' and optionally 'description'.
         * @returns A Promise that resolves with the created Board.
         *
         * @example
         * const newBoard = await boardStore.createBoard({ name: "New Board", description: "Optional description" });
         */
        async createBoard(data: {
            name: string;
            description?: string;
        }): Promise<Board> {
            this.loading = true;
            this.error = null;
            try {
                const response = await axios.post(route("boards.store"), data);
                const newBoard = response.data.board as Board;
                this.boards.push(newBoard);
                return newBoard;
            } catch (error: any) {
                this.error = "Error creating board.";
                console.error(error);
                throw error;
            } finally {
                this.loading = false;
            }
        },

        /**
         * Updates an existing board.
         *
         * Sends a PATCH request with the updated board data and updates the board in the store.
         *
         * @param boardId - The ID of the board to update.
         * @param data - An object with the updated properties (name and description).
         * @returns A Promise that resolves with the updated Board.
         *
         * @example
         * const updatedBoard = await boardStore.updateBoard(1, { name: "Updated Board", description: "New description" });
         */
        async updateBoard(
            boardId: number,
            data: { name: string; description?: string }
        ): Promise<Board> {
            this.loading = true;
            this.error = null;
            try {
                const response = await axios.patch(
                    route("boards.update", { board: boardId }),
                    data
                );
                const updatedBoard = response.data.board as Board;
                this.boards = this.boards.map((b) =>
                    b.id === boardId ? updatedBoard : b
                );
                return updatedBoard;
            } catch (error: any) {
                this.error = "Error updating board.";
                console.error(error);
                throw error;
            } finally {
                this.loading = false;
            }
        },

        /**
         * Deletes a board by its ID.
         *
         * @param boardId - The ID of the board to delete.
         * @returns A Promise that resolves when the board is deleted.
         */
        async deleteBoard(boardId: number): Promise<void> {
            this.loading = true;
            this.error = null;
            try {
                await axios.delete(route("boards.destroy", { board: boardId }));
                this.boards = this.boards.filter((b) => b.id !== boardId);
            } catch (error: any) {
                this.error = "Error deleting board.";
                console.error(error);
                throw error;
            } finally {
                this.loading = false;
            }
        },
    },
});
