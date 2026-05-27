<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Disciplina extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nombre',
        'descripcion',
        'costo',
        'tipo_costo',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'costo' => 'decimal:2',
        ];
    }

    public function horarios(): HasMany
    {
        return $this->hasMany(DisciplinaHorario::class)->orderByRaw("CASE dia_semana
            WHEN 'lunes' THEN 1 WHEN 'martes' THEN 2 WHEN 'miercoles' THEN 3
            WHEN 'jueves' THEN 4 WHEN 'viernes' THEN 5 WHEN 'sabado' THEN 6
            WHEN 'domingo' THEN 7 ELSE 8 END")->orderBy('hora_inicio');
    }

    public function profesores(): BelongsToMany
    {
        return $this->belongsToMany(Profesor::class, 'disciplina_profesor')
            ->withPivot('sueldo')
            ->withTimestamps()
            ->orderBy('apellido')
            ->orderBy('nombre');
    }

    public function socios(): BelongsToMany
    {
        return $this->belongsToMany(Socio::class, 'disciplina_socio')
            ->withPivot(['fecha_inscripcion', 'estado', 'beca'])
            ->withTimestamps()
            ->orderBy('apellido')
            ->orderBy('nombre');
    }

    public function sociosActivos(): BelongsToMany
    {
        return $this->socios()->wherePivot('estado', 'activa');
    }

    public static function etiquetaTipoCosto(string $tipo): string
    {
        return match ($tipo) {
            'mensual'    => 'Mensual',
            'por_clase'  => 'Por clase',
            'anual'      => 'Anual',
            default      => ucfirst($tipo),
        };
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
