<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/me', function (Request $request) {
    return \App\Models\User::where('telegram_id', $request->uid)->first();
});

Route::post('/telegram/webhook', [\App\Http\Controllers\TelegramController::class, 'handle']);

Route::get('/task', [\App\Http\Controllers\API\TaskController::class, 'index']);
Route::get('/task/project/{id}', [\App\Http\Controllers\API\TaskController::class, 'tasksForProject']);
Route::post('/task', [\App\Http\Controllers\API\TaskController::class, 'store']);
Route::put('/column-order/{project_id}', [\App\Http\Controllers\API\TaskController::class, 'columnSort']);
Route::get('/task/{id}', [\App\Http\Controllers\API\TaskController::class, 'show']);
Route::delete('/task/{id}', [\App\Http\Controllers\API\TaskController::class, 'delete']);
Route::patch('/task/{id}', [\App\Http\Controllers\API\TaskController::class, 'update']);
Route::post('/update/task/{id}', [\App\Http\Controllers\API\TaskController::class, 'update']);
Route::get('/get-column-id/{task_id}', [\App\Http\Controllers\API\TaskController::class, 'getColumnById']);
Route::get('/get-first-column/{project_id}', [\App\Http\Controllers\API\TaskController::class, 'getFirstColumn']);
Route::post('/activity', [\App\Http\Controllers\ActivityController::class, 'store']);
Route::put('/task/{id}/move', [\App\Http\Controllers\API\TaskController::class, 'move']);

// Задачи в календаре еще не добавлявляются и не обновляются
# TODO url из webapp
