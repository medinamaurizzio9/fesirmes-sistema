<?php

use App\Http\Controllers\AffiliateController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\CredentialController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SystemLogoController;
use Illuminate\Support\Facades\Route;

Route::get('/sistema/logo', [SystemLogoController::class, 'show'])->name('system.logo');

Route::middleware('guest')->group(function () {
    Route::get('/', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::get('/login', [AuthenticatedSessionController::class, 'create']);
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/actividades/reporte/general', [ActivityController::class, 'generalReport'])
        ->middleware('role:Administrador,SecretarÃ­a,Consulta')
        ->name('actividades.report.general');

    Route::get('/actividades/{activity}/asistencias', [AttendanceController::class, 'index'])
        ->middleware('role:Administrador,SecretarÃ­a,Consulta')
        ->name('actividades.asistencias.index');
    Route::get('/actividades/{activity}/asistencias/importar', [AttendanceController::class, 'importForm'])
        ->middleware('role:Administrador,SecretarÃ­a')
        ->name('actividades.asistencias.import.form');
    Route::post('/actividades/{activity}/asistencias/importar', [AttendanceController::class, 'import'])
        ->middleware('role:Administrador,SecretarÃ­a')
        ->name('actividades.asistencias.import');
    Route::get('/actividades/{activity}/asistencias/reporte', [AttendanceController::class, 'report'])
        ->middleware('role:Administrador,SecretarÃ­a,Consulta')
        ->name('actividades.asistencias.report');
    Route::get('/actividades/{activity}/asistencias/exportar', [AttendanceController::class, 'export'])
        ->middleware('role:Administrador,SecretarÃ­a,Consulta')
        ->name('actividades.asistencias.export');
    Route::post('/actividades/{activity}/asistencias/revertir', [AttendanceController::class, 'revert'])
        ->middleware('role:Administrador')
        ->name('actividades.asistencias.revert');

    Route::resource('actividades', ActivityController::class)
        ->parameters(['actividades' => 'activity'])
        ->middleware('role:Administrador,SecretarÃ­a,Consulta');

    Route::get('/configuracion/logo', [SystemLogoController::class, 'edit'])
        ->middleware('role:Administrador')
        ->name('settings.logo.edit');
    Route::post('/configuracion/logo', [SystemLogoController::class, 'update'])
        ->middleware('role:Administrador')
        ->name('settings.logo.update');
    Route::get('/configuracion/logo/png', [SystemLogoController::class, 'downloadPng'])
        ->middleware('role:Administrador')
        ->name('settings.logo.png');

    Route::get('/afiliados/{affiliate}/foto', [AffiliateController::class, 'photo'])
        ->middleware('role:Administrador,Secretaría,Consulta')
        ->name('afiliados.photo');
    Route::get('/afiliados/{affiliate}/credencial', [CredentialController::class, 'show'])
        ->middleware('role:Administrador,Secretaría,Consulta')
        ->name('afiliados.credential.show');
    Route::get('/afiliados/{affiliate}/credencial/pdf', [CredentialController::class, 'pdf'])
        ->middleware('role:Administrador,Secretaría,Consulta')
        ->name('afiliados.credential.pdf');
    Route::get('/afiliados/{affiliate}/credencial/imprimir', [CredentialController::class, 'print'])
        ->middleware('role:Administrador,Secretaría,Consulta')
        ->name('afiliados.credential.print');
    Route::post('/afiliados/{affiliate}/credencial/png/auditar', [CredentialController::class, 'auditPng'])
        ->middleware('role:Administrador,Secretaría,Consulta')
        ->name('afiliados.credential.png.audit');
    Route::post('/afiliados/{affiliate}/credencial/regenerar', [CredentialController::class, 'regenerate'])
        ->middleware('role:Administrador')
        ->name('afiliados.credential.regenerate');

    Route::resource('afiliados', AffiliateController::class)
        ->parameters(['afiliados' => 'affiliate'])
        ->middleware('role:Administrador,Secretaría,Consulta');
});
