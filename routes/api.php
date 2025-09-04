<?php
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Order\OrderController;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->group(function () {
    // Products
    Route::get('/products', [ProductController::class, 'index']);
    Route::post('/products', [ProductController::class, 'store'])->middleware('role:supplier');
    Route::put('/products/{id}', [ProductController::class, 'update'])->middleware('role:supplier');
    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->middleware('role:supplier');

    // Orders
    Route::post('/orders', [OrderController::class, 'store'])->middleware('role:pharmacy');
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus'])->middleware('role:supplier');
});
