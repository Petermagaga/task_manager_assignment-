<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Support\Facades\DB;

Route::prefix('tasks')->group(function () {

    Route::post('/', [TaskController::class, 'store']);
    Route::get('/', [TaskController::class, 'index']);
    Route::patch('{id}/status', [TaskController::class, 'updateStatus']);
    Route::delete('{id}', [TaskController::class, 'destroy']);
    Route::get('report', [TaskController::class, 'report']);

    Route::get('/debug-db', function () {
        return [
            'connection' => config('database.default'),
            'database' => config('database.connections.mysql.database'),
            'host' => config('database.connections.mysql.host'),
            'count' => \App\Models\Task::count()
        ];
    });

}); 