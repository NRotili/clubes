<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActividadTurno extends Model
{
    protected $fillable = [
        'actividad_id',
        'socio_id',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'estado',
        'monto',
        'pagado',
        'pago_id',
        'observaciones',
        'registrado_por',
        'gestionado_por',
    ];

    protected function casts(): array
    {
        return [
            'fecha'  => 'date:Y-m-d',
            'monto'  => 'decimal:2',
            'pagado' => 'boolean',
        ];
    }

    public function actividad(): BelongsTo
    {
        return $this->belongsTo(Actividad::class);
    }

    public function socio(): BelongsTo
    {
        return $this->belongsTo(Socio::class);
    }

    public function pago(): BelongsTo
    {
        return $this->belongsTo(Pago::class);
    }

    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    public function gestionadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'gestionado_por');
    }

    public function puedeCancelar(): bool
    {
        if (!in_array($this->estado, ['pendiente', 'confirmado'])) {
            return false;
        }

        $inicio = Carbon::parse($this->fecha->format('Y-m-d') . ' ' . $this->hora_inicio);

        return $inicio->isFuture();
    }

    public static function etiquetaEstado(string $estado): string
    {
        return match ($estado) {
            'pendiente'  => 'Pendiente',
            'confirmado' => 'Confirmado',
            'rechazado'  => 'Rechazado',
            'cancelado'  => 'Cancelado',
            default      => ucfirst($estado),
        };
    }
}
