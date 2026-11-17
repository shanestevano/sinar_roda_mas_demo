<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashierShift extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'shift_date',
        'opened_at',
        'closed_at',
        'starting_cash',
        'expected_cash',
        'actual_cash',
        'difference',
        'total_transactions',
        'total_cash_sales',
        'total_sales',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'shift_date' => 'date',
            'opened_at' => 'datetime',
            'closed_at' => 'datetime',
            'starting_cash' => 'decimal:2',
            'expected_cash' => 'decimal:2',
            'actual_cash' => 'decimal:2',
            'difference' => 'decimal:2',
            'total_cash_sales' => 'decimal:2',
            'total_sales' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'shift_id');
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }
}
