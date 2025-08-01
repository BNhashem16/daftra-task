<?php

namespace App\Http\Resources\Api\V1\Warehouse;

use App\Http\Resources\Api\V1\Stock\StockResource;
use Illuminate\Http\Resources\Json\JsonResource;

class WarehouseResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'location' => $this->location,
            'stocks' => StockResource::collection($this->whenLoaded('stocks')),
        ];
    }
}
