<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/projects', [\App\Http\Controllers\ProjectController::class, 'index'])->name('project.index');
Route::get('/project/{id}', [\App\Http\Controllers\ProjectController::class, 'show'])->name('project.show');

Route::view('/admin/login', 'admin.login')->name('admin.login');
Route::post('/admin/login', [\App\Http\Controllers\Admin\LoginController::class, 'login'])->name('admin.login');
Route::group(['middleware' => 'auth', 'prefix' => 'admin'], function (){
    Route::get('/', [\App\Http\Controllers\Admin\IndexController::class, 'index'])->name('admin.users');
    Route::get('/finances', [\App\Http\Controllers\Admin\FinanceController::class, 'index'])->name('admin.finance');
});
