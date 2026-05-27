<?php

use App\Models\ClubConfig;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Suspender deudores automáticamente el primer día de cada mes si el umbral > 0
Schedule::command('socios:suspender-deudores')
    ->monthlyOn(1, '08:00')
    ->when(fn() => ClubConfig::mesesSuspension() > 0)
    ->withoutOverlapping();

// Notificar vencimientos de cuotas (3 días antes, el día y el día después)
Schedule::command('socios:notificar-vencimientos')
    ->dailyAt('09:00')
    ->withoutOverlapping();
