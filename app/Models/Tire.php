<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tire extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'barcode',
        'brand',
        'pattern',
        'width',
        'aspect_ratio',
        'rim_size',
        'size_display',
        'type',
        'dot_code',
        'dot_year',
        'cost_price',
        'selling_price',
        'stock',
        'min_stock',
        'rack_location_id',
        'is_active',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'cost_price' => 'decimal:2',
            'selling_price' => 'decimal:2',
            'stock' => 'integer',
            'min_stock' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function rackLocation(): BelongsTo
    {
        return $this->belongsTo(RackLocation::class, 'rack_location_id');
    }

    public function mutations(): HasMany
    {
        return $this->hasMany(StockMutation::class)->latest();
    }

    public function transactionItems(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function inboundItems(): HasMany
    {
        return $this->hasMany(InboundItem::class);
    }

    public function isLowStock(): bool
    {
        return $this->stock <= $this->min_stock;
    }

    public function getDotAgeYearsAttribute(): ?int
    {
        if (! $this->dot_year) {
            return null;
        }

        $currentYear = (int) date('Y');

        return max(0, $currentYear - $this->dot_year);
    }

    public function isAgedOrExpired(int $maxYears = 3): bool
    {
        $age = $this->dot_age_years;

        return $age !== null && $age >= $maxYears;
    }

    public function getFormattedCostPriceAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->cost_price, 0, ',', '.');
    }

    public function getFormattedSellingPriceAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->selling_price, 0, ',', '.');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeLowStock(Builder $query): Builder
    {
        return $query->whereColumn('stock', '<=', 'min_stock');
    }

    public function scopeAgedDot(Builder $query, int $maxYears = 3): Builder
    {
        $cutoffYear = (int) date('Y') - $maxYears;

        return $query->whereNotNull('dot_year')->where('dot_year', '<=', $cutoffYear);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (! $term) {
            return $query;
        }

        $cleanedTerm = trim($term);

        return $query->where(function (Builder $q) use ($cleanedTerm) {
            $q->where('sku', 'like', "%{$cleanedTerm}%")
                ->orWhere('barcode', 'like', "%{$cleanedTerm}%")
                ->orWhere('brand', 'like', "%{$cleanedTerm}%")
                ->orWhere('pattern', 'like', "%{$cleanedTerm}%")
                ->orWhere('size_display', 'like', "%{$cleanedTerm}%")
                ->orWhere('dot_code', 'like', "%{$cleanedTerm}%");
        });
    }
}
