<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PagoItem extends Model
{
    protected $fillable = ['pago_id', 'descripcion', 'monto'];

    protected function casts(): array
    {
        return ['monto' => 'decimal:2'];
    }

    public function pago(): BelongsTo
    {
        return $this->belongsTo(Pago::class);
    }
}
