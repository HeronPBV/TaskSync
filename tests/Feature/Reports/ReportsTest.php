<?php

use App\Models\User;
use App\Jobs\GenerateDataReportJob;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class)->in(__DIR__);

/*
|--------------------------------------------------------------------------
| Report Controller Feature Tests
|--------------------------------------------------------------------------
|
| These tests verify the behavior of the report endpoints, ensuring that
| access control is enforced and that the correct data is returned.
|
*/

it('denies unauthenticated access to report index', function () {
    Cache::forget('latest_data_report');

    $response = $this->getJson(route('reports.index'));
    $response->assertStatus(401);
});

it('denies unauthenticated access to report generation', function () {
    $response = $this->postJson(route('reports.generate'));
    $response->assertStatus(401);
});

it('returns 404 when no report is available for an authenticated user', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    Cache::forget('latest_data_report');

    $response = $this->getJson(route('reports.index'));
    $response->assertStatus(404);
    $response->assertJson(['message' => 'No reports available.']);
});

it('returns report data when available for an authenticated user', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $fakeReport = [
        'total_boards' => 5,
        'total_columns' => 10,
        'total_tasks' => 50,
        'tasks_created_today' => 50,
        'tasks_deleted_today' => 0,
        'columns_created_today' => 10,
        'columns_deleted_today' => 0,
        'boards_created_today' => 5,
        'boards_deleted_today' => 0,
        'generated_at' => '2025-02-11 12:00:00',
    ];

    Cache::put('latest_data_report', $fakeReport, now()->addHour());

    $response = $this->getJson(route('reports.index'));
    $response->assertStatus(200);
    $response->assertJson(['report' => $fakeReport]);
});

it('dispatches a report generation job when requested by an authenticated user', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    // Fake the bus so that we can assert that the job is dispatched.
    Bus::fake();

    $response = $this->postJson(route('reports.generate'));
    $response->assertStatus(200);
    $response->assertJson(['message' => 'Report generation started.']);

    Bus::assertDispatched(GenerateDataReportJob::class);
});
