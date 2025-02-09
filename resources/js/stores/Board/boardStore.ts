import { defineStore } from "pinia";
import axios from "axios";
import { route } from "ziggy-js";

export interface Board {
    id: number;
    name: string;
    description?: string;
}

export const useBoardStore = defineStore("boardStore", {
    state: () => ({
        boards: [] as Board[],
        loading: false,
        error: null as string | null,
    }),
    actions: {
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
