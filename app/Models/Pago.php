<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pago extends Model
{
    protected $fillable = [
        'socio_id', 'cuota_mensual_id', 'fecha', 'metodo_pago', 'total', 'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'total' => 'decimal:2',
        ];
    }

    public function socio(): BelongsTo
    {
        return $this->belongsTo(Socio::class);
    }

    public function cuotaMensual(): BelongsTo
    {
        return $this->belongsTo(CuotaMensual::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PagoItem::class);
    }

    public static function etiquetaMetodo(string $metodo): string
    {
        return match ($metodo) {
            'efectivo'      => 'Efectivo',
            'transferencia' => 'Transferencia bancaria',
            'tarjeta_debito'=> 'Tarjeta de débito',
            default         => ucfirst($metodo),
        };
    }

    public static function metodos(): array
    {
        return ['efectivo', 'transferencia', 'tarjeta_debito'];
    }
}
