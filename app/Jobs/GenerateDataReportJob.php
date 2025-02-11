<?php

namespace App\Jobs;

use App\Models\Board;
use App\Models\Column;
use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Carbon\Carbon;

class GenerateDataReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Handle the job execution.
     *
     * This method generates report data by counting boards, columns, and tasks,
     * including statistics for today (created and deleted items). The report is then cached
     * for one hour. If an error occurs during generation, the error is logged and the existing
     * cache is maintained (if available).
     *
     * @return void
     */
    public function handle()
    {
        try {
            $today = Carbon::now()->toDateString();

            $reportData = [
                'total_boards' => Board::count(),
                'total_columns' => Column::count(),
                'total_tasks' => Task::count(),
                'tasks_created_today' => Task::whereDate('created_at', $today)->count(),
                'tasks_deleted_today' => Task::onlyTrashed()->whereDate('deleted_at', $today)->count(),
                'columns_created_today' => Column::whereDate('created_at', $today)->count(),
                'columns_deleted_today' => Column::onlyTrashed()->whereDate('deleted_at', $today)->count(),
                'boards_created_today' => Board::whereDate('created_at', $today)->count(),
                'boards_deleted_today' => Board::onlyTrashed()->whereDate('deleted_at', $today)->count(),
                'generated_at' => Carbon::now()->toDateTimeString(),
            ];

            Cache::put('latest_data_report', $reportData, Carbon::now()->addHour());

            Log::info('Report generated successfully.', $reportData);
        } catch (\Exception $e) {
            Log::error('Error generating report: ' . $e->getMessage());
        }
    }
}
