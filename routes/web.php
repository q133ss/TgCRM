<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


# TODO
/*
 * Делаем группы
 * Делаем ВЕБ АПП И ОБЫЧНОЕ ПРИЛОЖЕНИЕ 2 в 1
 * Будем проверять, сессию, если ее нет, то get params!
 */
