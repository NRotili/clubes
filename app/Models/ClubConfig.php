<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClubConfig extends Model
{
    protected $table      = 'club_config';
    protected $primaryKey = 'clave';
    public    $incrementing = false;
    protected $keyType    = 'string';

    protected $fillable = ['clave', 'valor'];

    public static function get(string $clave, mixed $default = null): mixed
    {
        return static::where('clave', $clave)->value('valor') ?? $default;
    }

    public static function set(string $clave, mixed $valor): void
    {
        static::updateOrCreate(['clave' => $clave], ['valor' => $valor]);
    }

    /** Día del mes en que vencen las cuotas (1-28). */
    public static function diaVencimiento(): int
    {
        return (int) static::get('cuota_dia_vencimiento', 10);
    }

    /** Porcentaje de recargo por mora (0 = sin recargo). */
    public static function recargoMora(): float
    {
        return (float) static::get('cuota_recargo_mora', 0);
    }

    /** Meses de deuda antes de suspensión automática (0 = desactivado). */
    public static function mesesSuspension(): int
    {
        return (int) static::get('meses_suspension', 3);
    }

    // ─── Datos del club ───────────────────────────────────────────────────────

    public static function nombre(): string
    {
        return static::get('club_nombre', config('app.name', 'Club'));
    }

    public static function logoPath(): ?string
    {
        return static::get('club_logo') ?: null;
    }

    public static function logoUrl(): ?string
    {
        $path = static::logoPath();
        return $path ? asset('storage/' . $path) : null;
    }

    public static function direccion(): string
    {
        return static::get('club_direccion', '');
    }

    public static function telefono(): string
    {
        return static::get('club_telefono', '');
    }

    public static function email(): string
    {
        return static::get('club_email', '');
    }

    public static function web(): string
    {
        return static::get('club_web', '');
    }

    public static function todos(): array
    {
        return [
            'nombre'    => static::nombre(),
            'logo_url'  => static::logoUrl(),
            'direccion' => static::direccion(),
            'telefono'  => static::telefono(),
            'email'     => static::email(),
            'web'       => static::web(),
        ];
    }
}
