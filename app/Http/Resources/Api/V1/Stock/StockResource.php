<?php

namespace App\Http\Resources\Api\V1\Stock;

use App\Http\Resources\Api\V1\Inventory\InventoryResource;
use App\Http\Resources\Api\V1\Warehouse\WarehouseResource;
use Illuminate\Http\Resources\Json\JsonResource;

class StockResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'warehouse' => new WarehouseResource($this->whenLoaded('warehouse')),
            'inventory_item' => new InventoryResource($this->whenLoaded('inventoryItem')),
            'quantity' => $this->quantity,
        ];
    }
}
