import type { Board } from "./Board";
import type { Column } from "@/interfaces/Column/Column";

export interface BoardWithDetails extends Board {
    columns: Column[];
}
