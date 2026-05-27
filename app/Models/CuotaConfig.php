<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CuotaConfig extends Model
{
    protected $fillable = ['categoria', 'genero', 'monto'];

    protected function casts(): array
    {
        return ['monto' => 'decimal:2'];
    }

    public static function categorias(): array
    {
        return ['adulto', 'junior', 'cadete', 'bebe', 'jubilado'];
    }

    public static function generos(): array
    {
        return ['M', 'F', 'X'];
    }

    public static function montoParaSocio(Socio $socio): float
    {
        return (float) static::where('categoria', $socio->categoria)
            ->where('genero', $socio->genero)
            ->value('monto') ?? 0;
    }

    /** Devuelve la grilla completa como array[categoria][genero] => monto */
    public static function grilla(): array
    {
        $registros = static::all()->keyBy(fn($r) => "{$r->categoria}_{$r->genero}");
        $grilla = [];
        foreach (static::categorias() as $cat) {
            foreach (static::generos() as $gen) {
                $grilla[$cat][$gen] = (float) ($registros["{$cat}_{$gen}"]->monto ?? 0);
            }
        }
        return $grilla;
    }

    /** Inicializa filas faltantes en la tabla */
    public static function inicializar(): void
    {
        foreach (static::categorias() as $cat) {
            foreach (static::generos() as $gen) {
                static::firstOrCreate(
                    ['categoria' => $cat, 'genero' => $gen],
                    ['monto' => 0]
                );
            }
        }
    }
}
