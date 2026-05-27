<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Socio extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'numero_socio',
        'nombre',
        'apellido',
        'tipo_documento',
        'numero_documento',
        'fecha_nacimiento',
        'genero',
        'email',
        'telefono',
        'celular',
        'direccion',
        'ciudad',
        'provincia',
        'codigo_postal',
        'categoria',
        'estado',
        'fecha_alta',
        'socio_titular_id',
        'parentesco',
        'observaciones',
        'foto',
        'qr_uuid',
        'paga_cuota_base',
    ];

    protected function casts(): array
    {
        return [
            'fecha_nacimiento' => 'date',
            'fecha_alta'       => 'date',
            'paga_cuota_base'  => 'boolean',
        ];
    }

    public function titular(): BelongsTo
    {
        return $this->belongsTo(Socio::class, 'socio_titular_id')->withTrashed();
    }

    public function grupoFamiliar(): HasMany
    {
        return $this->hasMany(Socio::class, 'socio_titular_id')->orderBy('apellido')->orderBy('nombre');
    }

    public function ingresos(): HasMany
    {
        return $this->hasMany(Ingreso::class)->orderByDesc('ingresado_en');
    }

    public function usuario(): HasOne
    {
        return $this->hasOne(User::class, 'socio_id');
    }

    public function cuotasMensuales(): HasMany
    {
        return $this->hasMany(CuotaMensual::class)->orderByDesc('periodo');
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class)->orderByDesc('fecha');
    }

    public function disciplinas(): BelongsToMany
    {
        return $this->belongsToMany(Disciplina::class, 'disciplina_socio')
            ->withPivot(['fecha_inscripcion', 'estado', 'beca'])
            ->withTimestamps()
            ->orderBy('nombre');
    }

    public function getRouteKeyName(): string
    {
        return 'qr_uuid';
    }

    public function esTitular(): bool
    {
        return is_null($this->socio_titular_id);
    }

    public function nombreCompleto(): string
    {
        return "{$this->apellido}, {$this->nombre}";
    }

    public function edad(): int
    {
        return $this->fecha_nacimiento->age;
    }

    public function fotoUrl(): ?string
    {
        return $this->foto ? asset('storage/' . $this->foto) : null;
    }

    public static function generarQrUuid(): string
    {
        do {
            $uuid = Str::uuid()->toString();
        } while (static::where('qr_uuid', $uuid)->exists());

        return $uuid;
    }

    public static function generarNumeroSocio(): string
    {
        $ultimo = static::withTrashed()->max('numero_socio');
        $siguiente = $ultimo ? (intval($ultimo) + 1) : 1;
        return str_pad($siguiente, 5, '0', STR_PAD_LEFT);
    }

    public static function titulares(): \Illuminate\Database\Eloquent\Builder
    {
        return static::whereNull('socio_titular_id')->orderBy('apellido')->orderBy('nombre');
    }

    public static function etiquetaCategoria(string $cat): string
    {
        return match ($cat) {
            'adulto'   => 'Adulto',
            'junior'   => 'Junior',
            'cadete'   => 'Cadete',
            'bebe'     => 'Bebé',
            'jubilado' => 'Jubilado',
            default    => ucfirst($cat),
        };
    }

    public static function etiquetaEstado(string $estado): string
    {
        return match ($estado) {
            'activo'     => 'Activo',
            'inactivo'   => 'Inactivo',
            'suspendido' => 'Suspendido',
            'pendiente'  => 'Pendiente',
            default      => ucfirst($estado),
        };
    }

    public static function etiquetaGenero(string $genero): string
    {
        return match ($genero) {
            'M' => 'Masculino',
            'F' => 'Femenino',
            'X' => 'No binario / Otro',
            default => $genero,
        };
    }

    public static function etiquetaParentesco(string $parentesco): string
    {
        return match ($parentesco) {
            'conyuge' => 'Cónyuge / Pareja',
            'hijo'    => 'Hijo/a',
            'padre'   => 'Padre / Madre',
            'hermano' => 'Hermano/a',
            'otro'    => 'Otro',
            default   => ucfirst($parentesco),
        };
    }
}
