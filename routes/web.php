<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\PasswordChangeController;
use App\Http\Controllers\HomeController;
use App\Http\Middleware\ForcePasswordChange;


Route::get('/', function () { return redirect('login');});

Auth::routes(['register'=>false]);

// Routes accessibles dès l’authentification (même si mot de passe à changer)
Route::middleware('auth')->group(function () {
    Route::get('change-password', [PasswordChangeController::class, 'showChangeForm'])->name('password.change.form');
    Route::post('change-password', [PasswordChangeController::class, 'changePassword'])->name('password.change');
});

// Routes protégées, uniquement accessibles si connecté ET mot de passe changé
Route::middleware(['auth', ForcePasswordChange::class])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::resource('year', \App\Http\Controllers\YearController::class);

    Route::get('disable/{year}', [YearController::class, 'disableyear'])->name('disable_year');
Route::get('activate/{year}', [YearController::class, 'activateyear'])->name('activate_year');


});

