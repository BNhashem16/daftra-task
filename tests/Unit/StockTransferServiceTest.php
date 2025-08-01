<?php

namespace Tests\Unit;

use App\Models\InventoryItem;
use App\Models\Stock;
use App\Models\User;
use App\Models\Warehouse;
use App\Services\StockTransferService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockTransferServiceTest extends TestCase
{
    use RefreshDatabase;

    private StockTransferService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new StockTransferService;
    }

    public function test_prevents_over_transfer()
    {
        $user = User::factory()->create();
        $fromWarehouse = Warehouse::factory()->create();
        $toWarehouse = Warehouse::factory()->create();
        $item = InventoryItem::factory()->create();

        // Create stock with only 5 items
        Stock::factory()->create([
            'warehouse_id' => $fromWarehouse->id,
            'inventory_item_id' => $item->id,
            'quantity' => 5,
        ]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Insufficient stock. Available: 5, Requested: 10');

        $this->service->transfer(
            $fromWarehouse->id,
            $toWarehouse->id,
            $item->id,
            10,
            $user->id
        );
    }

    public function test_successful_transfer_updates_quantities()
    {
        $user = User::factory()->create();
        $fromWarehouse = Warehouse::factory()->create();
        $toWarehouse = Warehouse::factory()->create();
        $item = InventoryItem::factory()->create();

        Stock::factory()->create([
            'warehouse_id' => $fromWarehouse->id,
            'inventory_item_id' => $item->id,
            'quantity' => 20,
        ]);

        $transfer = $this->service->transfer(
            $fromWarehouse->id,
            $toWarehouse->id,
            $item->id,
            5,
            $user->id
        );

        $this->assertDatabaseHas('stocks', [
            'warehouse_id' => $fromWarehouse->id,
            'inventory_item_id' => $item->id,
            'quantity' => 15,
        ]);

        $this->assertDatabaseHas('stocks', [
            'warehouse_id' => $toWarehouse->id,
            'inventory_item_id' => $item->id,
            'quantity' => 5,
        ]);
    }
}
