<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DisciplinaInscripcionLog extends Model
{
    public $timestamps = false;

    protected $fillable = ['socio_id', 'disciplina_id', 'accion', 'origen', 'registrado_por'];

    protected $casts = ['created_at' => 'datetime'];

    public function socio(): BelongsTo
    {
        return $this->belongsTo(Socio::class);
    }

    public function disciplina(): BelongsTo
    {
        return $this->belongsTo(Disciplina::class);
    }

    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }
}
