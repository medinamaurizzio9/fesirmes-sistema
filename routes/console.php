<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('about:fesirmes', function () {
    $this->info('FESIRMES Fase 1: autenticacion, roles, dashboard, afiliados y auditoria basica.');
})->purpose('Muestra informacion breve del sistema FESIRMES');
