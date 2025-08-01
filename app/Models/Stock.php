<?php

namespace App\Models;

use App\Events\LowStockDetectedEvent;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stock extends Model
{
    /** @use HasFactory<\Database\Factories\StockFactory> */
    use HasFactory;

    protected $fillable = [
        'warehouse_id',
        'inventory_item_id',
        'quantity',
    ];

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }

    #[Scope]
    public function hasQuantity(Builder $query): void
    {
        $query->where('quantity', '>', 0);
    }

    protected static function booted()
    {
        static::updated(function ($stock) {
            if ($stock->quantity <= $stock->inventoryItem->low_stock_threshold) {
                event(new LowStockDetectedEvent($stock));
            }
        });
    }

    public function updateQuantity(int $quantity): bool
    {
        if ($quantity < 0) {
            throw new \InvalidArgumentException('Quantity cannot be negative');
        }

        $this->quantity = $quantity;

        return $this->save();
    }

    public function increaseQuantity(int $amount): bool
    {
        return $this->updateQuantity($this->quantity + $amount);
    }

    public function decreaseQuantity(int $amount): bool
    {
        if ($amount > $this->quantity) {
            throw new \InvalidArgumentException('Insufficient stock available');
        }

        return $this->updateQuantity($this->quantity - $amount);
    }
}
