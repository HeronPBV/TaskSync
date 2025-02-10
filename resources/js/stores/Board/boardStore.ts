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
         * Deletes a board by its ID.
         *
         * @param boardId - The ID of the board to delete.
         * @returns A Promise that resolves when the board has been deleted.
         *
         * @example
         * await boardStore.deleteBoard(123);
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
