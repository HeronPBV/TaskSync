<?php

use Carbon\Carbon;
use App\Models\Task;
use App\Models\Board;
use App\Models\Column;
use App\Jobs\GenerateDataReportJob;
use Illuminate\Support\Facades\Cache;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class)->in(__DIR__);

beforeEach(function () {
    Carbon::setTestNow(Carbon::parse('2025-02-11 12:00:00'));

    Board::factory()->count(5)->create();
    Column::factory()->count(10)->create();
    Task::factory()->count(50)->create();
});

afterEach(function () {
    Carbon::setTestNow();
});

it('generates a valid report and caches it', function () {
    $job = new GenerateDataReportJob();
    $job->handle();

    $report = Cache::get('latest_data_report');

    expect($report)->toBeArray();
    expect($report)->toHaveKeys([
        'total_boards',
        'total_columns',
        'total_tasks',
        'tasks_created_today',
        'tasks_deleted_today',
        'columns_created_today',
        'columns_deleted_today',
        'boards_created_today',
        'boards_deleted_today',
        'generated_at',
    ]);

    expect($report['generated_at'])->toBe('2025-02-11 12:00:00');
});
