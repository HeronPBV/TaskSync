import type { Task } from "@/interfaces/Task/Task";

export interface Column {
    id: number;
    board_id: number;
    name: string;
    position?: number;
    tasks: Task[];
}
