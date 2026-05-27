<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DisciplinaHorario extends Model
{
    protected $fillable = [
        'disciplina_id',
        'dia_semana',
        'hora_inicio',
        'hora_fin',
    ];

    public function disciplina(): BelongsTo
    {
        return $this->belongsTo(Disciplina::class);
    }

    public function etiqueta(): string
    {
        return Disciplina::etiquetaDia($this->dia_semana)
            . ' ' . substr($this->hora_inicio, 0, 5)
            . ' – ' . substr($this->hora_fin, 0, 5);
    }

    /** Cuenta cuántas clases tiene una disciplina en el mes del período dado (formato Y-m). */
    public static function clasesEnPeriodo(int $disciplinaId, string $periodo): int
    {
        [$anio, $mes] = explode('-', $periodo);

        $mapaDias = [
            'lunes'     => 1,
            'martes'    => 2,
            'miercoles' => 3,
            'jueves'    => 4,
            'viernes'   => 5,
            'sabado'    => 6,
            'domingo'   => 0,
        ];

        $horarios = static::where('disciplina_id', $disciplinaId)->get();
        $inicio   = \Carbon\Carbon::create((int) $anio, (int) $mes, 1);
        $diasEnMes = $inicio->daysInMonth;

        $total = 0;
        foreach ($horarios as $horario) {
            $diaSemana = $mapaDias[$horario->dia_semana] ?? null;
            if ($diaSemana === null) continue;

            for ($d = 1; $d <= $diasEnMes; $d++) {
                if ($inicio->copy()->day($d)->dayOfWeek === $diaSemana) {
                    $total++;
                }
            }
        }

        return $total;
    }
}
