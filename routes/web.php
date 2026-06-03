<?php

use App\Http\Controllers\AffiliateController;
use App\Http\Controllers\AffiliatePortalController;
use App\Http\Controllers\AffiliateUserController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\CredentialController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InternalReportController;
use App\Http\Controllers\SindicatoController;
use App\Http\Controllers\SystemLogoController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::get('/login', [AuthenticatedSessionController::class, 'create']);
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::middleware(['role:Afiliado', 'affiliate.password.changed'])->group(function () {
        Route::get('/mi-perfil', [AffiliatePortalController::class, 'profile'])->name('affiliate.profile');
        Route::put('/mi-perfil', [AffiliatePortalController::class, 'updateProfile'])->name('affiliate.profile.update');
        Route::get('/mi-credencial', [CredentialController::class, 'showMine'])->name('affiliate.credential.show');
    });
    Route::middleware('role:Afiliado')->group(function () {
        Route::get('/mi-contrasena', [AffiliatePortalController::class, 'password'])->name('affiliate.password.edit');
        Route::put('/mi-contrasena', [AffiliatePortalController::class, 'updatePassword'])->name('affiliate.password.update');
    });

    Route::post('/afiliados/generar-usuarios', [AffiliateUserController::class, 'generate'])
        ->middleware('role:Administrador')
        ->name('afiliados.users.generate');
    Route::post('/afiliados/{affiliate}/usuario/reset', [AffiliateUserController::class, 'reset'])
        ->middleware('role:Administrador')
        ->name('afiliados.users.reset');
    Route::post('/afiliados/{affiliate}/usuario/bloquear', [AffiliateUserController::class, 'block'])
        ->middleware('role:Administrador')
        ->name('afiliados.users.block');
    Route::post('/afiliados/{affiliate}/usuario/desbloquear', [AffiliateUserController::class, 'unblock'])
        ->middleware('role:Administrador')
        ->name('afiliados.users.unblock');

    Route::prefix('reportes')->name('reportes.')->middleware('role:Administrador,Secretaría,Consulta')->group(function () {
        Route::get('/', [InternalReportController::class, 'index'])->name('index');
        Route::get('/padron', [InternalReportController::class, 'padron'])->name('padron');
        Route::get('/padron/csv', [InternalReportController::class, 'padronCsv'])->name('padron.csv');
        Route::get('/padron/pdf', [InternalReportController::class, 'padronPdf'])->name('padron.pdf');
        Route::get('/calidad-datos', [InternalReportController::class, 'quality'])->name('quality');
        Route::get('/calidad-datos/csv', [InternalReportController::class, 'qualityCsv'])->name('quality.csv');
        Route::get('/calidad-datos/pdf', [InternalReportController::class, 'qualityPdf'])->name('quality.pdf');
        Route::get('/sindicatos', [InternalReportController::class, 'sindicatos'])->name('sindicatos');
        Route::get('/sindicatos/csv', [InternalReportController::class, 'sindicatosCsv'])->name('sindicatos.csv');
        Route::get('/sindicatos/pdf', [InternalReportController::class, 'sindicatosPdf'])->name('sindicatos.pdf');
        Route::get('/tipos-item', [InternalReportController::class, 'itemTypes'])->name('item-types');
        Route::get('/tipos-item/csv', [InternalReportController::class, 'itemTypesCsv'])->name('item-types.csv');
        Route::get('/tipos-item/pdf', [InternalReportController::class, 'itemTypesPdf'])->name('item-types.pdf');
        Route::get('/asistencia-actividades', [InternalReportController::class, 'attendanceActivities'])->name('attendance.activities');
        Route::get('/asistencia-actividades/csv', [InternalReportController::class, 'attendanceActivitiesCsv'])->name('attendance.activities.csv');
        Route::get('/asistencia-actividades/pdf', [InternalReportController::class, 'attendanceActivitiesPdf'])->name('attendance.activities.pdf');
        Route::get('/asistencia-historica', [InternalReportController::class, 'attendanceHistory'])->name('attendance.history');
        Route::get('/asistencia-historica/csv', [InternalReportController::class, 'attendanceHistoryCsv'])->name('attendance.history.csv');
        Route::get('/asistencia-historica/pdf', [InternalReportController::class, 'attendanceHistoryPdf'])->name('attendance.history.pdf');
    });

    Route::get('/sindicatos/reporte/general', [SindicatoController::class, 'report'])
        ->middleware('role:Administrador,Secretaría,Consulta')
        ->name('sindicatos.report.general');
    Route::get('/sindicatos/reporte/asistencia', [SindicatoController::class, 'attendanceReport'])
        ->middleware('role:Administrador,Secretaría,Consulta')
        ->name('sindicatos.report.attendance');
    Route::post('/sindicatos/{sindicato}/activar', [SindicatoController::class, 'activate'])
        ->middleware('role:Administrador')
        ->name('sindicatos.activate');
    Route::resource('sindicatos', SindicatoController::class)
        ->middleware('role:Administrador,Secretaría,Consulta');

    Route::get('/actividades/reporte/general', [ActivityController::class, 'generalReport'])
        ->middleware('role:Administrador,Secretaría,Consulta')
        ->name('actividades.report.general');

    Route::get('/actividades/{activity}/asistencias', [AttendanceController::class, 'index'])
        ->middleware('role:Administrador,Secretaría,Consulta')
        ->name('actividades.asistencias.index');
    Route::get('/actividades/{activity}/asistencias/importar', [AttendanceController::class, 'importForm'])
        ->middleware('role:Administrador,Secretaría')
        ->name('actividades.asistencias.import.form');
    Route::post('/actividades/{activity}/asistencias/importar', [AttendanceController::class, 'import'])
        ->middleware('role:Administrador,Secretaría')
        ->name('actividades.asistencias.import');
    Route::get('/actividades/{activity}/asistencias/reporte', [AttendanceController::class, 'report'])
        ->middleware('role:Administrador,Secretaría,Consulta')
        ->name('actividades.asistencias.report');
    Route::get('/actividades/{activity}/asistencias/exportar', [AttendanceController::class, 'export'])
        ->middleware('role:Administrador,Secretaría,Consulta')
        ->name('actividades.asistencias.export');
    Route::post('/actividades/{activity}/asistencias/revertir', [AttendanceController::class, 'revert'])
        ->middleware('role:Administrador')
        ->name('actividades.asistencias.revert');

    Route::resource('actividades', ActivityController::class)
        ->parameters(['actividades' => 'activity'])
        ->middleware('role:Administrador,Secretaría,Consulta');

    Route::get('/configuracion/logo', [SystemLogoController::class, 'edit'])
        ->middleware('role:Administrador')
        ->name('settings.logo.edit');
    Route::post('/configuracion/logo', [SystemLogoController::class, 'update'])
        ->middleware('role:Administrador')
        ->name('settings.logo.update');
    Route::get('/configuracion/logo/png', [SystemLogoController::class, 'downloadPng'])
        ->middleware('role:Administrador')
        ->name('settings.logo.png');

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

