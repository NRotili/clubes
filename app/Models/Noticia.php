<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Noticia extends Model
{
    protected $fillable = ['titulo', 'cuerpo', 'publicado_por'];

    public function autor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'publicado_por');
    }
}
