<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    Auth\LoginController,
    HomeController,
    Admin\DashboardController
};
use App\Models\IpFailedLoginAttempt;

Route::get('/', function () {
    $ip = request()->ip();
    $lastLoginAttemptFromThisIp = IpFailedLoginAttempt::whereIp($ip)->orderBy('created_at', 'desc')->first();

    if($lastLoginAttemptFromThisIp) {
        if((int)$lastLoginAttemptFromThisIp->failed_attempt >= 5){
            return abort(403, 'Access Denied! Your IP has been BLOCKED due to a detected BRUTE FORCE ATTEMPT. To regain access, please CONTACT the administrator.');
        }
    }
    
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