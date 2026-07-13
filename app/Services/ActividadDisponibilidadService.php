<?php

namespace App\Services;

use App\Models\Actividad;
use App\Models\ActividadTurno;
use Carbon\Carbon;

class ActividadDisponibilidadService
{
    private const DIAS = [
        0 => 'domingo',
        1 => 'lunes',
        2 => 'martes',
        3 => 'miercoles',
        4 => 'jueves',
        5 => 'viernes',
        6 => 'sabado',
    ];

    public static function diaSemana(Carbon $fecha): string
    {
        return self::DIAS[$fecha->dayOfWeek];
    }

    /**
     * Genera los turnos posibles de una actividad para una fecha dada, junto con su cupo
     * disponible, a partir de las franjas configuradas para ese día de la semana.
     *
     * @return array<int, array{franja_id: int, hora_inicio: string, hora_fin: string, cupo: int, ocupados: int, disponibles: int}>
     */
    public static function slots(Actividad $actividad, Carbon $fecha): array
    {
        $diaSemana = self::diaSemana($fecha);

        $franjas = $actividad->franjas->where('dia_semana', $diaSemana);

        if ($franjas->isEmpty()) {
            return [];
        }

        $fechaStr = $fecha->format('Y-m-d');

        // Normaliza a formato H:i, ya que el valor crudo de la columna `time` puede venir
        // con o sin segundos según el motor de base de datos.
        $ocupados = [];
        foreach (ActividadTurno::where('actividad_id', $actividad->id)
            ->whereDate('fecha', $fechaStr)
            ->whereIn('estado', ['pendiente', 'confirmado'])
            ->get(['hora_inicio']) as $turno) {
            $clave = substr($turno->hora_inicio, 0, 5);
            $ocupados[$clave] = ($ocupados[$clave] ?? 0) + 1;
        }

        $esHoy = $fecha->isToday();
        $ahora = Carbon::now();

        $slots = [];

        foreach ($franjas as $franja) {
            $cursor = Carbon::parse($fechaStr . ' ' . $franja->hora_inicio);
            $fin    = Carbon::parse($fechaStr . ' ' . $franja->hora_fin);

            while ($cursor->copy()->addMinutes($franja->duracion_minutos)->lte($fin)) {
                $horaInicio = $cursor->format('H:i');
                $horaFin    = $cursor->copy()->addMinutes($franja->duracion_minutos)->format('H:i');

                // Para el día de hoy, no ofrecer turnos cuyo horario ya finalizó.
                if ($esHoy && Carbon::parse($fechaStr . ' ' . $horaFin)->lte($ahora)) {
                    $cursor->addMinutes($franja->duracion_minutos);

                    continue;
                }

                $ocupado = $ocupados[$horaInicio] ?? 0;

                $slots[] = [
                    'franja_id'   => $franja->id,
                    'hora_inicio' => $horaInicio,
                    'hora_fin'    => $horaFin,
                    'cupo'        => $franja->cupo,
                    'ocupados'    => $ocupado,
                    'disponibles' => max(0, $franja->cupo - $ocupado),
                ];

                $cursor->addMinutes($franja->duracion_minutos);
            }
        }

        return $slots;
    }

    /**
     * Busca, dentro de los slots disponibles para la fecha, el que arranca a la hora indicada.
     */
    public static function slotParaHora(Actividad $actividad, Carbon $fecha, string $horaInicio): ?array
    {
        $horaInicio = substr($horaInicio, 0, 5);

        foreach (self::slots($actividad, $fecha) as $slot) {
            if ($slot['hora_inicio'] === $horaInicio) {
                return $slot;
            }
        }

        return null;
    }
}
