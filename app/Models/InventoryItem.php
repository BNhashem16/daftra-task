<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryItem extends Model
{
    /** @use HasFactory<\Database\Factories\InventoryItemFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'sku',
        'description',
        'price',
        'low_stock_threshold',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    public function stockTransfers(): HasMany
    {
        return $this->hasMany(StockTransfer::class);
    }

    public function warehouses(): BelongsToMany
    {
        return $this->belongsToMany(Warehouse::class, 'stocks')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    #[Scope]
    public static function search(Builder $query, ?string $search): void
    {
        if (! $search) {
            return;
        }
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('sku', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        });
    }

    #[Scope]
    public static function priceRange(Builder $query, ?float $minPrice, ?float $maxPrice): void
    {
        $query
            ->when(! is_null($minPrice), fn ($q) => $q->where('price', '>=', $minPrice))
            ->when(! is_null($maxPrice), fn ($q) => $q->where('price', '<=', $maxPrice));
    }

    public function getTotalStockAttribute(): int
    {
        return $this->stocks->sum('quantity');
    }
}
