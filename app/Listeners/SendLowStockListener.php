<?php

namespace App\Listeners;

use App\Events\LowStockDetectedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendLowStockListener implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(LowStockDetectedEvent $event)
    {
        // In a real application, you would send an email here
        Log::info('Low stock detected', [
            'warehouse' => $event->stock->warehouse->name,
            'item' => $event->stock->inventoryItem->name,
            'current_quantity' => $event->stock->quantity,
            'threshold' => $event->stock->inventoryItem->low_stock_threshold,
        ]);

        // Mail::to('admin@example.com')->send(new LowStockEmail($event->stock));
    }
}
