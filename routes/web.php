<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    dd(\Carbon\Carbon::MONDAY);
    return view('welcome');
});
