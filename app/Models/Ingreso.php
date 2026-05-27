<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ingreso extends Model
{
    protected $fillable = ['socio_id', 'ingresado_en'];

    protected function casts(): array
    {
        return [
            'ingresado_en' => 'datetime',
        ];
    }

    public function socio(): BelongsTo
    {
        return $this->belongsTo(Socio::class);
    }
}
