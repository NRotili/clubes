<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CuotaMensual extends Model
{
    protected $table = 'cuotas_mensuales';

    protected $fillable = [
        'socio_id', 'periodo', 'items', 'monto_total', 'monto_pagado', 'estado',
    ];

    protected function casts(): array
    {
        return [
            'items'        => 'array',
            'monto_total'  => 'decimal:2',
            'monto_pagado' => 'decimal:2',
        ];
    }

    public function socio(): BelongsTo
    {
        return $this->belongsTo(Socio::class);
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class);
    }

    public function saldo(): float
    {
        return max(0, (float) $this->monto_total - (float) $this->monto_pagado);
    }

    public function fechaVencimiento(): \Carbon\Carbon
    {
        [$anio, $mes] = explode('-', $this->periodo);
        $dia = min(ClubConfig::diaVencimiento(), \Carbon\Carbon::create($anio, $mes)->daysInMonth);
        return \Carbon\Carbon::create($anio, $mes, $dia)->endOfDay();
    }

    public function estaVencida(): bool
    {
        return $this->estado !== 'pagado' && now()->gt($this->fechaVencimiento());
    }

    public function recargo(): float
    {
        if (!$this->estaVencida()) return 0;
        return round($this->saldo() * ClubConfig::recargoMora() / 100, 2);
    }

    public function recalcularEstado(): void
    {
        $pagado = (float) $this->pagos()->sum('total');
        $this->monto_pagado = $pagado;

        if ($pagado <= 0) {
            $this->estado = 'pendiente';
        } elseif ($pagado < (float) $this->monto_total) {
            $this->estado = 'parcial';
        } else {
            $this->estado = 'pagado';
        }

        $this->save();
    }

    public function periodoFormateado(): string
    {
        [$anio, $mes] = explode('-', $this->periodo);
        $meses = [
            '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo',
            '04' => 'Abril', '05' => 'Mayo', '06' => 'Junio',
            '07' => 'Julio', '08' => 'Agosto', '09' => 'Septiembre',
            '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre',
        ];
        return ($meses[$mes] ?? $mes) . ' ' . $anio;
    }

    public static function etiquetaEstado(string $estado): string
    {
        return match ($estado) {
            'pendiente' => 'Pendiente',
            'parcial'   => 'Pago parcial',
            'pagado'    => 'Pagado',
            default     => ucfirst($estado),
        };
    }

    public static function clasesEstado(string $estado): string
    {
        return match ($estado) {
            'pendiente' => 'bg-red-50 text-red-700 border-red-200',
            'parcial'   => 'bg-amber-50 text-amber-700 border-amber-200',
            'pagado'    => 'bg-green-50 text-green-700 border-green-200',
            default     => 'bg-slate-100 text-slate-600 border-slate-200',
        };
    }

    /** Genera cuotas para todos los socios activos en el período dado. */
    public static function generarParaPeriodo(string $periodo): array
    {
        $socios = Socio::where('estado', 'activo')
            ->with(['disciplinas' => fn($q) => $q->wherePivot('estado', 'activa')])
            ->get();

        $creadas  = 0;
        $omitidas = 0;

        foreach ($socios as $socio) {
            $existe = static::where('socio_id', $socio->id)
                ->where('periodo', $periodo)
                ->exists();

            if ($existe) {
                $omitidas++;
                continue;
            }

            $items = [];
            $total = 0;

            // Cuota base (solo si el socio la abona)
            if ($socio->paga_cuota_base) {
                $cuotaBase = CuotaConfig::montoParaSocio($socio);
                if ($cuotaBase > 0) {
                    $items[] = [
                        'descripcion' => 'Cuota base — ' . Socio::etiquetaCategoria($socio->categoria) . ' / ' . Socio::etiquetaGenero($socio->genero),
                        'monto'       => $cuotaBase,
                    ];
                    $total += $cuotaBase;
                }
            }

            // Disciplinas activas
            foreach ($socio->disciplinas as $d) {
                if ($d->pivot->beca) {
                    $items[] = [
                        'descripcion' => $d->nombre . ' (beca)',
                        'monto'       => 0,
                        'beca'        => true,
                    ];
                    continue;
                }

                if ($d->tipo_costo === 'por_clase') {
                    $clases     = DisciplinaHorario::clasesEnPeriodo($d->id, $periodo);
                    $costoClase = (float) $d->costo;
                    $monto      = round($costoClase * $clases, 2);
                    $items[] = [
                        'descripcion'        => $d->nombre . ' · ' . $clases . ' ' . ($clases === 1 ? 'clase' : 'clases') . ' × $' . number_format($costoClase, 2, ',', '.'),
                        'monto'              => $monto,
                        'tipo'               => 'por_clase',
                        'costo_clase'        => $costoClase,
                        'clases'             => $clases,
                        'clases_programadas' => $clases,
                        'disciplina_id'      => $d->id,
                    ];
                } else {
                    $monto = match ($d->tipo_costo) {
                        'mensual' => (float) $d->costo,
                        'anual'   => round((float) $d->costo / 12, 2),
                        default   => (float) $d->costo,
                    };
                    $items[] = [
                        'descripcion' => $d->nombre . ($d->tipo_costo === 'anual' ? ' (anual ÷12)' : ''),
                        'monto'       => $monto,
                    ];
                }
                $total += $monto;
            }

            if (empty($items)) {
                $omitidas++;
                continue;
            }

            static::create([
                'socio_id'    => $socio->id,
                'periodo'     => $periodo,
                'items'       => $items,
                'monto_total' => $total,
                'monto_pagado'=> 0,
                'estado'      => 'pendiente',
            ]);

            $creadas++;
        }

        return compact('creadas', 'omitidas');
    }
}
