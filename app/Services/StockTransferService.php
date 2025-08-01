<?php

namespace App\Services;

use App\Models\Stock;
use App\Models\StockTransfer;
use Illuminate\Support\Facades\DB;

class StockTransferService
{
    public function transfer(
        int $fromWarehouseId,
        int $toWarehouseId,
        int $inventoryItemId,
        int $quantity,
        int $userId,
    ): StockTransfer {
        return DB::transaction(function () use (
            $fromWarehouseId,
            $toWarehouseId,
            $inventoryItemId,
            $quantity,
            $userId,
        ) {
            $fromStock = Stock::firstOrCreate([
                'warehouse_id' => $fromWarehouseId,
                'inventory_item_id' => $inventoryItemId,
            ], ['quantity' => 0]);

            $toStock = Stock::firstOrCreate([
                'warehouse_id' => $toWarehouseId,
                'inventory_item_id' => $inventoryItemId,
            ], ['quantity' => 0]);

            if ($fromStock->quantity < $quantity) {
                throw new \InvalidArgumentException(
                    "Insufficient stock. Available: {$fromStock->quantity}, Requested: {$quantity}"
                );
            }

            $fromStock->decreaseQuantity($quantity);
            $toStock->increaseQuantity($quantity);

            return StockTransfer::create([
                'from_warehouse_id' => $fromWarehouseId,
                'to_warehouse_id' => $toWarehouseId,
                'inventory_item_id' => $inventoryItemId,
                'quantity' => $quantity,
                'user_id' => $userId,
            ]);
        });
    }
}
