<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UsersController;
use App\Http\Controllers\Api\TimeLogController;
use App\Http\Controllers\Api\ProjectsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('register', [AuthController::class, 'register'])->name('register');


Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('users', [UsersController::class, 'index']);

    Route::resource('projects', ProjectsController::class);

    Route::get('time-log/evaluation', [TimeLogController::class, 'evaluation']);
    Route::get('time-log/status', [TimeLogController::class, 'status'])->name('time-log.status');
    Route::get('time-log/export-csv', [TimeLogController::class, 'export'])->name('export.csv');
    Route::post('time-log/{id?}', [TimeLogController::class, 'log']);
    Route::get('time-log/{id}', [TimeLogController::class, 'get']);
    Route::delete('time-log/{id}', [TimeLogController::class, 'delete']);
    Route::get('time-log', [TimeLogController::class, 'index']);

});
