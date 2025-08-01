<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StockTransferRequest;
use App\Http\Resources\Api\V1\StockTransfer\StockTransferResource;
use App\Services\StockTransferService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controllers\HasMiddleware;

class StockTransferController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'auth:sanctum',
        ];
    }

    public function __invoke(StockTransferRequest $request, StockTransferService $stockTransferService): JsonResponse
    {
        try {
            $transfer = $stockTransferService->transfer(
                $request->from_warehouse_id,
                $request->to_warehouse_id,
                $request->inventory_item_id,
                $request->quantity,
                auth('sanctum')->id(),
            );

            $transfer->load(['fromWarehouse', 'toWarehouse', 'inventoryItem']);
            $transfer = StockTransferResource::make($transfer);

            return $this->ok(message: 'Stock transfer completed successfully', data: $transfer);
        } catch (\InvalidArgumentException $e) {
            return $this->unprocessable(message: $e->getMessage());
        }
    }
}
