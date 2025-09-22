<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quotation extends Model
{
    protected $fillable = [
        'fecha','ciudad','departamento','dirigido_a','objeto','notas'
    ];

    protected $casts = [
        'fecha' => 'date',
        'notas' => 'array',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function total(): int
    {
        return (int) $this->items()->sum('vr_total');
    }
}

