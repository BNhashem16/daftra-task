<?php

namespace App\Http\Resources\Api\V1\Inventory;

use App\Http\Resources\Api\V1\Stock\StockResource;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'sku' => $this->sku,
            'low_stock_threshold' => $this->low_stock_threshold,
            'description' => $this->description,
            'price' => $this->price,
            'total_stock' => $this->total_stock,
            'stocks' => StockResource::collection($this->whenLoaded('stocks')),
        ];
    }
}
