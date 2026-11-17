<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inbound extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_number',
        'supplier_id',
        'supplier_invoice_number',
        'received_date',
        'user_id',
        'total_amount',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'received_date' => 'date',
            'total_amount' => 'decimal:2',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InboundItem::class);
    }

    public function getFormattedTotalAmountAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->total_amount, 0, ',', '.');
    }
}
