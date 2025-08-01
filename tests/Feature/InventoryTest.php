<?php

namespace Tests\Feature;

use App\Models\InventoryItem;
use App\Models\Stock;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class InventoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_paginated_inventory()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $warehouse = Warehouse::factory()->create();
        $items = InventoryItem::factory()->count(25)->create();

        foreach ($items as $item) {
            Stock::factory()->create([
                'warehouse_id' => $warehouse->id,
                'inventory_item_id' => $item->id,
                'quantity' => rand(1, 100),
            ]);
        }
        $response = $this->getJson('/api/v1/inventory?per_page=10');

        $response->assertStatus(200);
        $this->assertEquals(25, $response->json('data.meta.total'));
        $this->assertEquals(10, count($response->json('data.data')));
    }

    public function test_can_search_inventory_by_name()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $warehouse = Warehouse::factory()->create();
        $laptop = InventoryItem::factory()->create(['name' => 'Gaming Laptop']);
        $mouse = InventoryItem::factory()->create(['name' => 'Wireless Mouse']);

        Stock::factory()->create([
            'warehouse_id' => $warehouse->id,
            'inventory_item_id' => $laptop->id,
            'quantity' => 10,
        ]);

        Stock::factory()->create([
            'warehouse_id' => $warehouse->id,
            'inventory_item_id' => $mouse->id,
            'quantity' => 20,
        ]);

        $response = $this->getJson('/api/v1/inventory?search=laptop');
        $response->assertStatus(200);
        $data = $response->json('data.data');

        $this->assertCount(1, $data);
        $this->assertEquals('Gaming Laptop', $data[0]['name']);
    }

    public function test_can_filter_by_price_range()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $warehouse = Warehouse::factory()->create();
        $cheapItem = InventoryItem::factory()->create(['price' => 50.00]);
        $expensiveItem = InventoryItem::factory()->create(['price' => 500.00]);

        Stock::factory()->create([
            'warehouse_id' => $warehouse->id,
            'inventory_item_id' => $cheapItem->id,
            'quantity' => 10,
        ]);

        Stock::factory()->create([
            'warehouse_id' => $warehouse->id,
            'inventory_item_id' => $expensiveItem->id,
            'quantity' => 5,
        ]);

        $response = $this->getJson('/api/v1/inventory?min_price=100&max_price=600');
        $response->assertStatus(200);
        $data = $response->json('data.data');

        $this->assertCount(1, $data);
        $this->assertEquals(500.00, $data[0]['price']);
    }

    public function test_warehouse_inventory_endpoint()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $warehouse = Warehouse::factory()->create(['name' => 'Main Warehouse']);
        $item1 = InventoryItem::factory()->create(['name' => 'Product A']);
        $item2 = InventoryItem::factory()->create(['name' => 'Product B']);

        Stock::factory()->create([
            'warehouse_id' => $warehouse->id,
            'inventory_item_id' => $item1->id,
            'quantity' => 15,
        ]);

        Stock::factory()->create([
            'warehouse_id' => $warehouse->id,
            'inventory_item_id' => $item2->id,
            'quantity' => 25,
        ]);

        $response = $this->getJson("/api/v1/warehouses/{$warehouse->id}/inventory");
        $response->assertStatus(200);

        $this->assertEquals('Main Warehouse', $response->json('data.warehouse.name'));
        $this->assertEquals(2, $response->json('data.total_items'));
        $this->assertEquals(40, $response->json('data.total_quantity'));
    }
}
