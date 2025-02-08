export interface Task {
    id: number;
    column_id: number;
    title: string;
    description?: string;
    due_date?: string;
    priority?: string;
    position?: number;
}
