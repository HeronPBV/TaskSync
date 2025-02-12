<?php


use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\ColumnController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

require __DIR__ . '/auth.php';

Route::get('/', [HomeController::class, 'home'])
    ->name('home');

Route::middleware('auth')->group(function () {

    //Home Routes
    Route::get('/dashboard', [HomeController::class, 'dashboard'])
        ->name('dashboard');


    //Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    // API Throttling
    Route::middleware('throttle:60,1')->group(function () {

        // Boards, Columns, and Tasks routes
        Route::apiResource('boards', BoardController::class)
            ->except('index');

        Route::apiResource('boards.columns', ColumnController::class)
            ->except('index', 'show')->shallow();

        Route::patch('columns/{column}/tasks/reorder', [TaskController::class, 'reorder'])
            ->name('tasks.reorder');

        Route::apiResource('columns.tasks', TaskController::class)
            ->except('index', 'show')->shallow();


        //User Routes
        Route::get('/user', [UserController::class, 'index'])
            ->name('user.index');


        //Report Routes
        Route::get('/reports', [ReportController::class, 'index'])
            ->name('reports.index');

        Route::post('/reports/generate', [ReportController::class, 'generate'])
            ->name('reports.generate');

    });

    Route::get('/dashboard/reports', [ReportController::class, 'show'])
        ->name('reports.show');

});
