<?php

use App\Http\Controllers\AffiliateController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::get('/login', [AuthenticatedSessionController::class, 'create']);
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/afiliados/{affiliate}/foto', [AffiliateController::class, 'photo'])
        ->middleware('role:Administrador,Secretaría,Consulta')
        ->name('afiliados.photo');

    Route::resource('afiliados', AffiliateController::class)
        ->parameters(['afiliados' => 'affiliate'])
        ->middleware('role:Administrador,Secretaría,Consulta');
});
