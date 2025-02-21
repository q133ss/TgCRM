<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/telegram/webhook', [\App\Http\Controllers\TelegramController::class, 'handle']);
