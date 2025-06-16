<?php

use App\Http\Controllers\HistoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Support\Facades\Route;

Route::apiResource('warehouses', WarehouseController::class);
Route::apiResource('products', ProductController::class);
Route::prefix('orders')->apiResource('orders', OrderController::class);
Route::prefix('orders')->controller(OrderController::class)->group(callback: function () {
    Route::get('/{order}', 'one');
    Route::post('/complete', 'complete');
    Route::post('/cancel', 'cancel');
    Route::post('/continue', 'continue');
});
Route::apiResource('histories', HistoryController::class);
