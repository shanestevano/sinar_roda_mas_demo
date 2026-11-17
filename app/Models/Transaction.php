<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'user_id',
        'shift_id',
        'customer_name',
        'customer_phone',
        'vehicle_plate',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'grand_total',
        'payment_method',
        'amount_paid',
        'change_amount',
        'status',
        'void_reason',
        'voided_by',
        'voided_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'grand_total' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'change_amount' => 'decimal:2',
            'voided_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(CashierShift::class, 'shift_id');
    }

    public function voidedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'voided_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isVoided(): bool
    {
        return $this->status === 'voided';
    }

    public function getTotalCostAttribute(): float
    {
        return (float) $this->items->sum(function (TransactionItem $item) {
            return (float) $item->cost_price * $item->quantity;
        });
    }

    public function getGrossProfitAttribute(): float
    {
        if ($this->isVoided()) {
            return 0;
        }

        return (float) $this->grand_total - $this->total_cost;
    }

    public function getFormattedGrandTotalAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->grand_total, 0, ',', '.');
    }

    public function getFormattedAmountPaidAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->amount_paid, 0, ',', '.');
    }

    public function getFormattedChangeAmountAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->change_amount, 0, ',', '.');
    }
}
