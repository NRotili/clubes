<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comunicacion extends Model
{
    protected $table = 'comunicaciones';

    protected $fillable = [
        'usuario_id', 'asunto', 'cuerpo', 'tipo',
        'destinatario_tipo', 'filtro', 'enviados', 'fallidos',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public static function etiquetaDestinatario(string $tipo, ?string $filtro): string
    {
        return match ($tipo) {
            'todos'     => 'Todos los socios activos',
            'deudores'  => 'Deudores',
            'categoria' => 'Categoría: ' . Socio::etiquetaCategoria($filtro ?? ''),
            'socio'     => 'Socio individual',
            default     => $tipo,
        };
    }
}
