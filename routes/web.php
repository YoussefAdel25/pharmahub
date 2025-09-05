<?php

use App\Http\Controllers\Cart\CartController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Order\OrderController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Supplier\SupplierController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});




Route::middleware('auth')->group(function () {

    //admin
    //supplier

    Route::get('products', [ProductController::class, 'index'])->name('products.index');

    Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
    Route::get('products/{product}', [ProductController::class, 'show']);
    Route::post('products/store', [ProductController::class, 'store'])->name('');
    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{product}', [ProductController::class, 'show']);
    Route::get('orders/create', [ProductController::class, 'create']);


    Route::get('Suppliers', [SupplierController::class, 'indexSuppliers'])->name('suppliers.index');
    Route::get('Suppliers/{supplier}', [SupplierController::class, 'show']);
    Route::get('Supplier/Products/{supplier}', [SupplierController::class, 'showSupplierProducts'])->name('supplier.products');

    Route::post('/cart/add', [CartController::class, 'add']);
    Route::post('/cart/index', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/update', [CartController::class, 'update']);
    Route::get('/cart/items', [CartController::class, 'items'])->name('cart.items');

    Route::get('orders', [OrderController::class, 'index'])->name('cart.add');
    Route::get('orders/{order}', [OrderController::class, 'show']);
    Route::get('orders/create', [OrderController::class, 'create']);
});

require __DIR__ . '/auth.php';
