<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Enterprise-Level: Exam Auto-Save to Redis Route
Route::post('/exam/answer/save', [\App\Http\Controllers\Api\ExamController::class, 'autoSave'])->name('api.exam.autosave');

// Enterprise-Level: Keamanan & Observabilitas
Route::post('/exam/heartbeat', [\App\Http\Controllers\Api\HeartbeatController::class, 'ping'])->name('api.exam.heartbeat');
Route::post('/exam/violation/log', [\App\Http\Controllers\Api\ViolationController::class, 'logViolation'])->name('api.exam.violation');

// Enterprise-Level: Fitur Ragu-Ragu & Offline Sync
Route::post('/exam/answer/doubtful', [\App\Http\Controllers\Api\ExamController::class, 'markDoubtful'])->name('api.exam.doubtful');
Route::post('/exam/answer/bulk-sync', [\App\Http\Controllers\Api\ExamController::class, 'bulkSync'])->name('api.exam.bulksync');
