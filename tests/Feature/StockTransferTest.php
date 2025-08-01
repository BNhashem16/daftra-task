<?php

namespace Tests\Feature;

use App\Models\InventoryItem;
use App\Models\Stock;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StockTransferTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_stock_transfer()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $fromWarehouse = Warehouse::factory()->create();
        $toWarehouse = Warehouse::factory()->create();
        $item = InventoryItem::factory()->create();

        Stock::factory()->create([
            'warehouse_id' => $fromWarehouse->id,
            'inventory_item_id' => $item->id,
            'quantity' => 50,
        ]);

        $response = $this->postJson('/api/v1/stock-transfers', [
            'from_warehouse_id' => $fromWarehouse->id,
            'to_warehouse_id' => $toWarehouse->id,
            'inventory_item_id' => $item->id,
            'quantity' => 10,
            'notes' => 'Test transfer',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('stock_transfers', [
            'from_warehouse_id' => $fromWarehouse->id,
            'to_warehouse_id' => $toWarehouse->id,
            'inventory_item_id' => $item->id,
            'quantity' => 10,
            'user_id' => $user->id,
        ]);
    }

    public function test_unauthenticated_user_cannot_create_transfer()
    {
        $fromWarehouse = Warehouse::factory()->create();
        $toWarehouse = Warehouse::factory()->create();
        $item = InventoryItem::factory()->create();

        $response = $this->postJson('/api/v1/stock-transfers', [
            'from_warehouse_id' => $fromWarehouse->id,
            'to_warehouse_id' => $toWarehouse->id,
            'inventory_item_id' => $item->id,
            'quantity' => 10,
        ]);

        $response->assertStatus(401);
    }

    public function test_validation_prevents_same_warehouse_transfer()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $warehouse = Warehouse::factory()->create();
        $item = InventoryItem::factory()->create();
        $response = $this->postJson('/api/v1/stock-transfers', [
            'from_warehouse_id' => $warehouse->id,
            'to_warehouse_id' => $warehouse->id,
            'inventory_item_id' => $item->id,
            'quantity' => 10,
        ]);
        $response->assertStatus(422);
    }
}
