<?php

namespace Database\Seeders;

use App\Models\InventoryItem;
use App\Models\Stock;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class InventoryItemSeeder extends Seeder
{
    // To run this seeder run the following command:
    // php artisan db:seed --class=InventoryItemSeeder
    public function run()
    {
        // Create admin user
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);

        // Create warehouses
        $warehouses = [
            ['name' => 'Main Warehouse', 'location' => 'El Sheikh Zayed - Cairo'],
            ['name' => 'West Coast Hub', 'location' => '6th of October - Cairo'],
            ['name' => 'Distribution Center', 'location' => 'Nasr City - Cairo'],
        ];

        foreach ($warehouses as $warehouseData) {
            Warehouse::create($warehouseData);
        }

        // Create inventory items
        $items = [
            [
                'name' => 'Gaming Laptop',
                'sku' => 'LAPTOP-001',
                'price' => 1299.99,
                'description' => 'High-performance gaming laptop with 16GB RAM and 512GB SSD.',
                'low_stock_threshold' => 2,
            ],
            [
                'name' => 'Wireless Mouse',
                'sku' => 'MOUSE-001',
                'price' => 29.99,
                'description' => 'Wireless mouse with 2.4GHz connectivity and ergonomic design.',
                'low_stock_threshold' => 5,
            ],
            [
                'name' => 'Mechanical Keyboard',
                'sku' => 'KEYBOARD-001',
                'price' => 149.99,
                'description' => 'Mechanical keyboard with RGB backlight and 104-key layout.',
                'low_stock_threshold' => 3,
            ],
            [
                'name' => 'Office Chair',
                'sku' => 'CHAIR-001',
                'price' => 299.99,
                'description' => 'Adjustable office chair with lumbar support and mesh back.',
                'low_stock_threshold' => 1,
            ],
            [
                'name' => 'Standing Desk',
                'sku' => 'DESK-001',
                'price' => 499.99,
                'description' => 'Standing desk with adjustable height and ergonomic features.',
                'low_stock_threshold' => 2,
            ],
        ];

        foreach ($items as $itemData) {
            InventoryItem::create($itemData);
        }

        // Create stock records
        $warehouses = Warehouse::all();
        $items = InventoryItem::all();

        foreach ($warehouses as $warehouse) {
            foreach ($items as $item) {
                Stock::create([
                    'warehouse_id' => $warehouse->id,
                    'inventory_item_id' => $item->id,
                    'quantity' => rand(20, 100),
                ]);
            }
        }
    }
}
