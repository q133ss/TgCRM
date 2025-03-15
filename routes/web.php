<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/projects', [\App\Http\Controllers\ProjectController::class, 'index'])->name('project.index');
Route::get('/project/{id}', [\App\Http\Controllers\ProjectController::class, 'show'])->name('project.show');

Route::get('/')->name('auth');

Route::view('/admin/login', 'admin.login')->name('admin.login');
Route::post('/admin/login', [\App\Http\Controllers\Admin\LoginController::class, 'login'])->name('admin.login');
Route::group(['middleware' => 'auth', 'prefix' => 'admin'], function (){
    Route::get('/', [\App\Http\Controllers\Admin\IndexController::class, 'index'])->name('admin.users');
    Route::get('/finances', [\App\Http\Controllers\Admin\FinanceController::class, 'index'])->name('admin.finance');
});

Route::group(['prefix' => 'dashboard', 'as' => 'dashboard.'], function (){
    Route::get('/', [\App\Http\Controllers\Dashboard\IndexController::class, 'index'])->name('index');
    Route::get('/calendar', [\App\Http\Controllers\Dashboard\CalendarController::class, 'index'])->name('calendar');
    Route::get('/tasks', [\App\Http\Controllers\Dashboard\TaskController::class, 'index'])->name('tasks');
    Route::get('/projects', [\App\Http\Controllers\Dashboard\ProjectController::class, 'index'])->name('projects');
    Route::get('/project/{id}', [\App\Http\Controllers\Dashboard\ProjectController::class, 'show'])->name('projects.show');
    Route::get('/users', [\App\Http\Controllers\Dashboard\IndexController::class, 'index'])->name('users');
    Route::get('/faq', [\App\Http\Controllers\Dashboard\IndexController::class, 'index'])->name('faq');
});
