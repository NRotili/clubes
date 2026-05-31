<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Profesor extends Model
{
    use SoftDeletes;

    protected $table = 'profesores';

    protected $fillable = [
        'nombre',
        'apellido',
        'email',
        'telefono',
        'celular',
        'cuil',
        'estado',
        'observaciones',
    ];

    public function usuario(): HasOne
    {
        return $this->hasOne(User::class, 'profesor_id');
    }

    public function disciplinas(): BelongsToMany
    {
        return $this->belongsToMany(Disciplina::class, 'disciplina_profesor')
            ->withPivot('sueldo')
            ->withTimestamps()
            ->orderBy('nombre');
    }

    public function nombreCompleto(): string
    {
        return "{$this->apellido}, {$this->nombre}";
    }

    public static function etiquetaEstado(string $estado): string
    {
        return match ($estado) {
            'activo'   => 'Activo',
            'inactivo' => 'Inactivo',
            default    => ucfirst($estado),
        };
    }
}
