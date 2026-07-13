<?php

namespace App\Services;

use App\Models\Actividad;
use App\Models\ActividadFranja;
use App\Models\ActividadTurno;
use App\Models\Socio;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ActividadReservaService
{
    /**
     * Crea una reserva de turno para un socio, validando disponibilidad, anticipación y
     * límites configurados en la actividad.
     */
    public static function reservar(
        Actividad $actividad,
        Socio $socio,
        string $fecha,
        string $horaInicio,
        ?int $registradoPor = null,
        ?string $observaciones = null,
        bool $bypassLimites = false,
    ): ActividadTurno {
        $fechaCarbon = Carbon::parse($fecha)->startOfDay();
        $horaInicio  = substr($horaInicio, 0, 5);

        $actividad->loadMissing('disciplinasRequeridas');
        if ($actividad->disciplinasRequeridas->isNotEmpty()) {
            $disciplinasDelSocio = $socio->disciplinas()->pluck('disciplinas.id');
            $tieneAlguna = $actividad->disciplinasRequeridas->pluck('id')->intersect($disciplinasDelSocio)->isNotEmpty();
            if (!$tieneAlguna) {
                $nombres = $actividad->disciplinasRequeridas->pluck('nombre')->join(', ');
                throw ValidationException::withMessages([
                    'turno' => "Para reservar esta actividad debés estar inscripto en: {$nombres}.",
                ]);
            }
        }

        if (!$bypassLimites) {
            if ($fechaCarbon->isBefore(today())) {
                throw ValidationException::withMessages([
                    'fecha' => 'No se puede reservar una fecha pasada.',
                ]);
            }

            if ($actividad->anticipacion_dias !== null) {
                $maxFecha = today()->addDays($actividad->anticipacion_dias);
                if ($fechaCarbon->gt($maxFecha)) {
                    throw ValidationException::withMessages([
                        'fecha' => "Esta actividad solo admite reservas con hasta {$actividad->anticipacion_dias} día(s) de anticipación.",
                    ]);
                }
            }

            if ($actividad->max_turnos_activos !== null) {
                $activos = ActividadTurno::where('actividad_id', $actividad->id)
                    ->where('socio_id', $socio->id)
                    ->whereIn('estado', ['pendiente', 'confirmado'])
                    ->whereDate('fecha', '>=', today())
                    ->count();

                if ($activos >= $actividad->max_turnos_activos) {
                    throw ValidationException::withMessages([
                        'turno' => 'Alcanzaste el límite de turnos activos para esta actividad.',
                    ]);
                }
            }
        }

        return DB::transaction(function () use ($actividad, $socio, $fechaCarbon, $horaInicio, $registradoPor, $observaciones) {
            // Bloquea las franjas del día para serializar la verificación de cupo entre
            // reservas concurrentes sobre el mismo horario.
            ActividadFranja::where('actividad_id', $actividad->id)
                ->where('dia_semana', ActividadDisponibilidadService::diaSemana($fechaCarbon))
                ->lockForUpdate()
                ->get();

            $slot = ActividadDisponibilidadService::slotParaHora($actividad, $fechaCarbon, $horaInicio);

            if (!$slot) {
                throw ValidationException::withMessages([
                    'hora_inicio' => 'El horario seleccionado no está disponible para esta actividad.',
                ]);
            }

            if ($slot['disponibles'] <= 0) {
                throw ValidationException::withMessages([
                    'hora_inicio' => 'No hay cupo disponible para el turno seleccionado.',
                ]);
            }

            return ActividadTurno::create([
                'actividad_id'   => $actividad->id,
                'socio_id'       => $socio->id,
                'fecha'          => $fechaCarbon->format('Y-m-d'),
                'hora_inicio'    => $slot['hora_inicio'],
                'hora_fin'       => $slot['hora_fin'],
                'estado'         => $actividad->requiere_aprobacion ? 'pendiente' : 'confirmado',
                'monto'          => $actividad->requiere_pago ? $actividad->costo : null,
                'pagado'         => false,
                'observaciones'  => $observaciones,
                'registrado_por' => $registradoPor,
            ]);
        });
    }
}
