<?php

use App\Http\Controllers\Api\V1\InventoryController;
use App\Http\Controllers\Api\V1\LoginController;
use App\Http\Controllers\Api\V1\StockTransferController;
use App\Http\Controllers\Api\V1\WarehouseController;
use Illuminate\Support\Facades\Route;

Route::post('login', LoginController::class);
Route::get('inventory', InventoryController::class);
Route::post('stock-transfers', StockTransferController::class);
Route::get('warehouses/{warehouse}/inventory', WarehouseController::class);
