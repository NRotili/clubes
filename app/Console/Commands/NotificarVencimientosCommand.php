<?php

namespace App\Console\Commands;

use App\Models\ClubConfig;
use App\Models\CuotaMensual;
use App\Services\PushNotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class NotificarVencimientosCommand extends Command
{
    protected $signature   = 'socios:notificar-vencimientos';
    protected $description = 'Envía push notifications por cuotas próximas a vencer y vencidas';

    public function handle(): int
    {
        $dia = ClubConfig::diaVencimiento();
        $hoy = Carbon::today();

        $enviadas = 0;

        // ── 3 días antes del vencimiento ────────────────────────────────────────
        $en3 = $hoy->copy()->addDays(3);
        if ($en3->day === min($dia, $en3->daysInMonth)) {
            $cuotas = CuotaMensual::where('periodo', $en3->format('Y-m'))
                ->whereIn('estado', ['pendiente', 'parcial'])
                ->with('socio')
                ->get();

            foreach ($cuotas as $cuota) {
                PushNotificationService::enviarAlSocio(
                    $cuota->socio,
                    'Tu cuota vence en 3 días',
                    "La cuota de {$cuota->periodoFormateado()} vence el {$en3->format('d/m')}. Saldo: $" . number_format($cuota->saldo(), 2, ',', '.'),
                    ['tipo' => 'vencimiento_proximo', 'cuota_id' => (string) $cuota->id]
                );
                $enviadas++;
            }
        }

        // ── El día del vencimiento ───────────────────────────────────────────────
        if ($hoy->day === min($dia, $hoy->daysInMonth)) {
            $cuotas = CuotaMensual::where('periodo', $hoy->format('Y-m'))
                ->whereIn('estado', ['pendiente', 'parcial'])
                ->with('socio')
                ->get();

            foreach ($cuotas as $cuota) {
                PushNotificationService::enviarAlSocio(
                    $cuota->socio,
                    'Tu cuota vence hoy',
                    "La cuota de {$cuota->periodoFormateado()} vence hoy. Saldo: $" . number_format($cuota->saldo(), 2, ',', '.'),
                    ['tipo' => 'vencimiento_hoy', 'cuota_id' => (string) $cuota->id]
                );
                $enviadas++;
            }
        }

        // ── El día después del vencimiento (cuota vencida) ──────────────────────
        $ayer = $hoy->copy()->subDay();
        if ($ayer->day === min($dia, $ayer->daysInMonth)) {
            $cuotas = CuotaMensual::where('periodo', $ayer->format('Y-m'))
                ->whereIn('estado', ['pendiente', 'parcial'])
                ->with('socio')
                ->get();

            foreach ($cuotas as $cuota) {
                PushNotificationService::enviarAlSocio(
                    $cuota->socio,
                    'Cuota vencida',
                    "Tu cuota de {$cuota->periodoFormateado()} está vencida. Regularizá tu situación para evitar la suspensión.",
                    ['tipo' => 'vencida', 'cuota_id' => (string) $cuota->id]
                );
                $enviadas++;
            }
        }

        $this->info("✓ {$enviadas} notificaciones enviadas.");

        return self::SUCCESS;
    }
}
