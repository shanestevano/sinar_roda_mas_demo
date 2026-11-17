<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMutation extends Model
{
    use HasFactory;

    protected $fillable = [
        'tire_id',
        'type', // inbound, sale, adjustment, void_return
        'quantity',
        'stock_before',
        'stock_after',
        'reference_type',
        'reference_id',
        'user_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'stock_before' => 'integer',
            'stock_after' => 'integer',
        ];
    }

    public function tire(): BelongsTo
    {
        return $this->belongsTo(Tire::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getTypeBadgeColorAttribute(): string
    {
        return match ($this->type) {
            'inbound' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            'sale' => 'bg-blue-100 text-blue-800 border-blue-300',
            'void_return' => 'bg-amber-100 text-amber-800 border-amber-300',
            'adjustment' => 'bg-purple-100 text-purple-800 border-purple-300',
            default => 'bg-slate-100 text-slate-800 border-slate-300',
        };
    }

    public function getTypeNameAttribute(): string
    {
        return match ($this->type) {
            'inbound' => 'Barang Masuk',
            'sale' => 'Penjualan Kasir',
            'void_return' => 'Retur Void Transaksi',
            'adjustment' => 'Penyesuaian Opname',
            default => ucfirst($this->type),
        };
    }
}
