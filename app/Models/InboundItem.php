<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InboundItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'inbound_id',
        'tire_id',
        'quantity',
        'cost_price',
        'subtotal',
        'dot_code',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'cost_price' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    public function inbound(): BelongsTo
    {
        return $this->belongsTo(Inbound::class);
    }

    public function tire(): BelongsTo
    {
        return $this->belongsTo(Tire::class);
    }
}
