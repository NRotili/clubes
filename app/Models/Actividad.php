<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Actividad extends Model
{
    use SoftDeletes;

    protected $table = 'actividades';

    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
        'requiere_aprobacion',
        'requiere_pago',
        'costo',
        'anticipacion_dias',
        'max_turnos_activos',
    ];

    protected function casts(): array
    {
        return [
            'requiere_aprobacion' => 'boolean',
            'requiere_pago'       => 'boolean',
            'costo'               => 'decimal:2',
        ];
    }

    public function franjas(): HasMany
    {
        return $this->hasMany(ActividadFranja::class)->orderByRaw("CASE dia_semana
            WHEN 'lunes' THEN 1 WHEN 'martes' THEN 2 WHEN 'miercoles' THEN 3
            WHEN 'jueves' THEN 4 WHEN 'viernes' THEN 5 WHEN 'sabado' THEN 6
            WHEN 'domingo' THEN 7 ELSE 8 END")->orderBy('hora_inicio');
    }

    public function turnos(): HasMany
    {
        return $this->hasMany(ActividadTurno::class);
    }

    public function disciplinasRequeridas(): BelongsToMany
    {
        return $this->belongsToMany(Disciplina::class, 'actividad_disciplina');
    }

    public static function diasOrdenados(): array
    {
        return ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];
    }

    public static function etiquetaDia(string $dia): string
    {
        return match ($dia) {
            'lunes'     => 'Lunes',
            'martes'    => 'Martes',
            'miercoles' => 'Miércoles',
            'jueves'    => 'Jueves',
            'viernes'   => 'Viernes',
            'sabado'    => 'Sábado',
            'domingo'   => 'Domingo',
            default     => ucfirst($dia),
        };
    }
}
