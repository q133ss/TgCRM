<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/projects', [\App\Http\Controllers\ProjectController::class, 'index'])->name('project.index');
Route::get('/project/{id}', [\App\Http\Controllers\ProjectController::class, 'show'])->name('project.show');


# TODO
/*
 * Делаем группы (И кнопка активировать меня в чате)
 * Делаем ВЕБ АПП И ОБЫЧНОЕ ПРИЛОЖЕНИЕ 2 в 1
 * Будем проверять, сессию, если ее нет, то get params!
 */
