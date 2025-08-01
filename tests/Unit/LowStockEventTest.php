<?php

namespace Tests\Unit;

use App\Events\LowStockDetectedEvent;
use App\Listeners\SendLowStockListener;
use App\Models\InventoryItem;
use App\Models\Stock;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class LowStockEventTest extends TestCase
{
    use RefreshDatabase;

    public function test_low_stock_event_is_fired_when_stock_drops_below_threshold()
    {
        Event::fake([LowStockDetectedEvent::class]);

        $warehouse = Warehouse::factory()->create();
        $item = InventoryItem::factory()->create(['low_stock_threshold' => 10]);

        $stock = Stock::factory()->create([
            'warehouse_id' => $warehouse->id,
            'inventory_item_id' => $item->id,
            'quantity' => 15,
        ]);

        // Update stock to below threshold
        $stock->update(['quantity' => 5]);

        Event::assertDispatched(LowStockDetectedEvent::class, function ($event) use ($stock) {
            return $event->stock->id === $stock->id;
        });
    }

    public function test_low_stock_event_is_not_fired_when_stock_is_above_threshold()
    {
        Event::fake([LowStockDetectedEvent::class]);

        $warehouse = Warehouse::factory()->create();
        $item = InventoryItem::factory()->create(['low_stock_threshold' => 10]);

        $stock = Stock::factory()->create([
            'warehouse_id' => $warehouse->id,
            'inventory_item_id' => $item->id,
            'quantity' => 5,
        ]);

        // Update stock to above threshold
        $stock->update(['quantity' => 15]);

        Event::assertNotDispatched(LowStockDetectedEvent::class);
    }

    public function test_low_stock_listener_logs_notification()
    {
        Log::shouldReceive('info')->once()->with('Low stock detected', \Mockery::type('array'));

        $warehouse = Warehouse::factory()->create(['name' => 'Test Warehouse']);
        $item = InventoryItem::factory()->create([
            'name' => 'Test Item',
            'low_stock_threshold' => 10,
        ]);

        $stock = Stock::factory()->create([
            'warehouse_id' => $warehouse->id,
            'inventory_item_id' => $item->id,
            'quantity' => 5,
        ]);

        $event = new LowStockDetectedEvent($stock);
        $listener = new SendLowStockListener;

        $listener->handle($event);
    }
}
