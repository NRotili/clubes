<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CajaApertura extends Model
{
    protected $table = 'caja_aperturas';

    protected $fillable = [
        'monto', 'fecha', 'descripcion', 'user_id',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'monto' => 'decimal:2',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** La apertura vigente es la última cargada (permite corregir dejando historial). */
    public static function vigente(): ?self
    {
        return static::latest('id')->first();
    }
}
