<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/projects', [\App\Http\Controllers\ProjectController::class, 'index'])->name('project.index');
Route::get('/project/{id}', [\App\Http\Controllers\ProjectController::class, 'show'])->name('project.show');

# TODO
/*
 * ДЕЛАЕМ ОБРАБОТКУ ОШИБОК try catch в чате! Что бы на длинный текст, он отвечал "произошла ошибка"!!
 * НА МОБИЛКЕ БУДЕТ ТОЛЬКО ОДНА КОЛОНКА! СВЕРХУ БУДЕТ СЕЛЕКТ С КОЛОНКАМИ, А В УПРАВЛЕНИИ ЗАДАЧЕЙ МОЖНО БУДЕТ ПЕРЕМЕСТИТЬ ЕЕ ДРУГУЮ КОЛОНКУ ЧЕРЕЗ СЕКЛЕКТ!
 * Делаем группы (И кнопка активировать меня в чате)
 * Делаем ВЕБ АПП И ОБЫЧНОЕ ПРИЛОЖЕНИЕ 2 в 1
 * Будем проверять, сессию, если ее нет, то get params!
 */
