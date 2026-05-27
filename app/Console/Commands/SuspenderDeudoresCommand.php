<?php

namespace App\Console\Commands;

use App\Models\ClubConfig;
use App\Models\Socio;
use App\Services\PushNotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SuspenderDeudoresCommand extends Command
{
    protected $signature   = 'socios:suspender-deudores {--dry-run : Mostrar quiénes serían suspendidos sin aplicar cambios}';
    protected $description = 'Suspende socios activos con N o más cuotas impagas consecutivas';

    public function handle(): int
    {
        $meses   = ClubConfig::mesesSuspension();
        $dryRun  = $this->option('dry-run');

        $this->info("Umbral configurado: {$meses} cuotas impagas para suspensión.");

        $candidatos = Socio::where('estado', 'activo')
            ->whereHas(
                'cuotasMensuales',
                fn($q) => $q->whereIn('estado', ['pendiente', 'parcial']),
                '>=',
                $meses
            )
            ->with(['cuotasMensuales' => fn($q) => $q->whereIn('estado', ['pendiente', 'parcial'])])
            ->get();

        if ($candidatos->isEmpty()) {
            $this->info('No hay socios para suspender.');
            return self::SUCCESS;
        }

        $this->table(
            ['N° Socio', 'Nombre', 'Cuotas impagas'],
            $candidatos->map(fn($s) => [
                $s->numero_socio,
                $s->nombreCompleto(),
                $s->cuotasMensuales->count(),
            ])
        );

        if ($dryRun) {
            $this->warn("Modo dry-run: no se aplicaron cambios. Se suspenderían {$candidatos->count()} socios.");
            return self::SUCCESS;
        }

        $suspendidos = 0;
        foreach ($candidatos as $socio) {
            $socio->update(['estado' => 'suspendido']);
            PushNotificationService::enviarAlSocio(
                $socio,
                'Cuenta suspendida',
                'Tu cuenta fue suspendida por cuotas impagas. Contactá a la administración para regularizar tu situación.',
                ['tipo' => 'suspension']
            );
            $suspendidos++;
        }

        $this->info("✓ {$suspendidos} " . ($suspendidos === 1 ? 'socio suspendido' : 'socios suspendidos') . '.');

        return self::SUCCESS;
    }
}
