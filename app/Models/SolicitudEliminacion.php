<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SolicitudEliminacion extends Model
{
    protected $table = 'solicitudes_eliminacion';

    protected $fillable = [
        'nombre',
        'identificador',
        'motivo',
        'estado',
        'procesada_en',
        'procesada_por',
    ];

    protected function casts(): array
    {
        return [
            'procesada_en' => 'datetime',
        ];
    }

    public function procesadaPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'procesada_por');
    }

    public function marcarProcesada(User $usuario): void
    {
        $this->update([
            'estado'        => 'procesada',
            'procesada_en'  => now(),
            'procesada_por' => $usuario->id,
        ]);
    }
}
