<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AsistenciaDisciplina extends Model
{
    protected $table = 'asistencia_disciplina';

    protected $fillable = ['disciplina_id', 'socio_id', 'fecha', 'registrado_por'];

    protected $casts = ['fecha' => 'date'];

    public function disciplina(): BelongsTo
    {
        return $this->belongsTo(Disciplina::class);
    }

    public function socio(): BelongsTo
    {
        return $this->belongsTo(Socio::class);
    }
}
