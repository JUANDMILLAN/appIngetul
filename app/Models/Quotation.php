<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Quotation extends Model
{
    protected $fillable = [
        
        'user_id',        
        'consecutivo',
        'fecha','ciudad','departamento','dirigido_a','referente',  'objeto','notas','estado'
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
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}

