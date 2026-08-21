<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\ActivityController;
use App\Http\Controllers\Api\CheckpointController;
use App\Http\Controllers\Api\RoundLogController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotas de Autenticação
|--------------------------------------------------------------------------
*/

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

/*
|--------------------------------------------------------------------------
| Rotas protegidas da SPA (API)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->prefix('api')->group(function () {
    // Usuário autenticado
    Route::get('/user', [AuthController::class, 'user']);
    Route::get('dashboard/stats', [UserController::class, 'stats']);

    Route::get('rounds', [RoundLogController::class, 'index']);
    Route::post('rounds', [RoundLogController::class, 'store']);
    
    Route::get('rounds/{roundLog}', [RoundLogController::class, 'show']);

    Route::middleware('role:admin|developer')->group(function () {
        Route::apiResource('checkpoints', CheckpointController::class);
        Route::get('checkpoints/{checkpoint}/qrcode', [CheckpointController::class, 'qrcode']);
        Route::get('checkpoints/{checkpoint}/print', [CheckpointController::class, 'print'])->name('checkpoints.print');

        Route::delete('rounds/{roundLog}', [RoundLogController::class, 'destroy']);
    });

    Route::middleware('role:admin|developer')->group(function () {
        Route::get('activity-log', [ActivityController::class, 'activityLog']);
        Route::apiResource('users', UserController::class);
        Route::get('roles', [UserController::class, 'roles']);
    });
});

/*
|--------------------------------------------------------------------------
| Catch-all da SPA
|--------------------------------------------------------------------------
*/

Route::view('/{any?}', 'app')->where('any', '.*');
