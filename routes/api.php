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
Route::post('/task/web/{id}', [\App\Http\Controllers\API\TaskController::class, 'updateForWeb']);
Route::post('/update/task/{id}', [\App\Http\Controllers\API\TaskController::class, 'update']); // Что это?
Route::get('/get-column-id/{task_id}', [\App\Http\Controllers\API\TaskController::class, 'getColumnById']);
Route::get('/get-first-column/{project_id}', [\App\Http\Controllers\API\TaskController::class, 'getFirstColumn']);
Route::post('/activity', [\App\Http\Controllers\ActivityController::class, 'store']);
Route::put('/task/{id}/move', [\App\Http\Controllers\API\TaskController::class, 'move']);
Route::delete('/file/{id}', [\App\Http\Controllers\API\FileController::class, 'delete']);
Route::get('/project/{id}/responsible', [\App\Http\Controllers\API\TaskController::class, 'responsibleForProject']);
Route::get('/task/{id}/activity', [\App\Http\Controllers\API\TaskController::class, 'activity']);
Route::resource('column', \App\Http\Controllers\API\ColumnController::class);

// Kanban
// TODO : Добавить время задачи + дату настроить что бы все работало!
// TODO : Добавить колонку
// TODO : Удалить/Переименовать колонку
// TODO : Переместить колонку

// Задачи в календаре еще не добавлявляются и не обновляются
# TODO url из webapp
