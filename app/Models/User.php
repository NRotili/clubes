<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Profesor;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'password',
        'rol',
        'socio_id',
        'profesor_id',
        'expo_push_token',
        'noticias_leidas_hasta',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function socio(): BelongsTo
    {
        return $this->belongsTo(Socio::class, 'socio_id');
    }

    public function profesor(): BelongsTo
    {
        return $this->belongsTo(Profesor::class, 'profesor_id');
    }

    public function esDesarrollador(): bool
    {
        return $this->rol === 'desarrollador';
    }

    public function esAdministracion(): bool
    {
        return $this->rol === 'administracion';
    }

    public function esSocio(): bool
    {
        return $this->rol === 'socio';
    }

    public function esProfesor(): bool
    {
        return $this->rol === 'profesor';
    }

    public function puedeGestionarSocios(): bool
    {
        return in_array($this->rol, ['desarrollador', 'administracion']);
    }

    public static function etiquetaRol(string $rol): string
    {
        return match ($rol) {
            'desarrollador' => 'Desarrollador',
            'administracion' => 'Administración',
            'socio'         => 'Socio',
            'profesor'      => 'Profesor',
            default         => ucfirst($rol),
        };
    }
}
