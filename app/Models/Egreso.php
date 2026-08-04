<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Egreso extends Model
{
    protected $fillable = [
        'descripcion', 'monto', 'fecha', 'categoria', 'observaciones', 'profesor_id',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'monto' => 'decimal:2',
        ];
    }

    public static function categorias(): array
    {
        return [
            'sueldos' => 'Sueldos',
            'alquiler' => 'Alquiler',
            'servicios' => 'Servicios',
            'mantenimiento' => 'Mantenimiento',
            'suministros' => 'Suministros',
            'impuestos' => 'Impuestos',
            'otros' => 'Otros',
        ];
    }

    public static function etiquetaCategoria(string $cat): string
    {
        return static::categorias()[$cat] ?? ucfirst($cat);
    }

    public function profesor(): BelongsTo
    {
        return $this->belongsTo(Profesor::class);
    }
}
