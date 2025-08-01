<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\CacheKeyPrefixEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Stock\StockResource;
use App\Http\Resources\Api\V1\Warehouse\WarehouseResource;
use App\Models\Warehouse;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Cache;

class WarehouseController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'auth:sanctum',
        ];
    }

    public function __invoke(Warehouse $warehouse): JsonResponse
    {
        $cacheKey = CacheKeyPrefixEnum::WAREHOUSE->value.md5("_{$warehouse->id}");
        $stocks = Cache::remember($cacheKey, fn () => config('cache.ttl.warehouse'), function () use ($warehouse) {
            return $warehouse->stocks()->with(['inventoryItem'])->hasQuantity()->get();
        });
        $data = [
            'warehouse' => WarehouseResource::make($warehouse),
            'inventory' => StockResource::collection($stocks),
            'total_items' => $stocks->count(),
            'total_quantity' => $stocks->sum('quantity'),
        ];

        return $this->ok(data: $data);
    }
}
