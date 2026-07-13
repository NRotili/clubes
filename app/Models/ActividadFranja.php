<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActividadFranja extends Model
{
    protected $fillable = [
        'actividad_id',
        'dia_semana',
        'hora_inicio',
        'hora_fin',
        'duracion_minutos',
        'cupo',
    ];

    public function actividad(): BelongsTo
    {
        return $this->belongsTo(Actividad::class);
    }

    public function etiqueta(): string
    {
        return Actividad::etiquetaDia($this->dia_semana)
            . ' ' . substr($this->hora_inicio, 0, 5)
            . ' – ' . substr($this->hora_fin, 0, 5)
            . ' (turnos de ' . $this->duracion_minutos . ' min, cupo ' . $this->cupo . ')';
    }
}
