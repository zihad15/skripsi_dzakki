<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    Auth\LoginController,
    HomeController,
    Admin\DashboardController
};

Route::get('/', function () {
    return view('welcome');
});

Auth::routes([
    "register" => false
]);

Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/datatablehistory', [DashboardController::class, 'ipFailedLoginAttemptData'])->name('datatablehistory');
Route::get('/datatablelist', [DashboardController::class, 'ipFailedLoginAttemptListData'])->name('datatablelist');

Route::post('/block', [DashboardController::class, 'block'])->name('block');
Route::post('/unblock', [DashboardController::class, 'unblock'])->name('unblock');