<?php

namespace App\Http\Resources\Api\V1\StockTransfer;

use Illuminate\Http\Resources\Json\JsonResource;

class StockTransferResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'from_warehouse' => $this->fromWarehouse->name,
            'to_warehouse' => $this->toWarehouse->name,
            'inventory_item' => $this->inventoryItem->name,
            'quantity' => $this->quantity,
            'created_at' => $this->created_at,
        ];
    }
}
