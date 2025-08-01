<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\CacheKeyPrefixEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\InventorySearchRequest;
use App\Http\Resources\Api\V1\Inventory\InventoryResourceCollection;
use App\Models\InventoryItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Cache;

class InventoryController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'auth:sanctum',
        ];
    }

    public function __invoke(InventorySearchRequest $request): JsonResponse
    {
        $cacheKey = CacheKeyPrefixEnum::INVENTORY->value.md5(serialize($request->validated()));
        $items = Cache::remember($cacheKey, fn () => config('cache.ttl.inventory'), function () use ($request) {
            $query = InventoryItem::with(['stocks.warehouse'])
                ->search($request->search)
                ->priceRange($request->min_price, $request->max_price);

            return $query->paginate($request->input('per_page', 15));
        });

        return $this->ok(data: new InventoryResourceCollection($items));
    }
}
