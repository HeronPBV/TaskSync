export interface Report {
    total_boards: number;
    total_columns: number;
    total_tasks: number;
    tasks_created_today: number;
    tasks_deleted_today: number;
    columns_created_today: number;
    columns_deleted_today: number;
    boards_created_today: number;
    boards_deleted_today: number;
    generated_at: string;
}
