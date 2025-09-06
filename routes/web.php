<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\homeController;
use Monolog\Handler\RotatingFileHandler;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Cart\CartController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Order\OrderController;
use App\Http\Controllers\cart\CheckoutController;
use App\Http\Controllers\Region\RegionController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\Supplier\SupplierController;
use App\Http\Controllers\order\SupplierOrdersController;


Route::get('/', [homeController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});




Route::middleware('auth')->group(function () {

    Route::get('/products/by-region', [ProductController::class, 'getByRegion']);
    Route::post('/cart/checkout', [CheckoutController::class, 'checkout'])->name('cart.checkout');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/supplier/{id}/products', [SupplierController::class, 'products'])->name('supplier.products');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

Route::prefix('admin')->group(function () {
    // Users Management
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users/store', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/edit/{user}', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/update/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/bulk-delete', [UserController::class, 'bulkDelete'])->name('users.bulkDelete');

    // Products Management
    Route::get('/products', [ProductController::class, 'allProducts'])->name('allProducts.index');

    // Orders Management
    Route::get('/orders', [OrderController::class, 'allOrders'])->name('allOrders.index');
});


    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/regions', [RegionController::class, 'index'])->name('region.index');
    Route::get('/regions/create', [RegionController::class, 'create'])->name('region.create');
    Route::post('/regions/store', [RegionController::class, 'store'])->name('region.store');
    Route::put('/regions/edit/{region}', [RegionController::class, 'edit'])->name('region.edit');
    Route::put('/regions/update/{region}', [RegionController::class, 'update'])->name('region.update');
    Route::delete('/regions/delete/{region}', [RegionController::class, 'destroy'])->name('region.destroy');
    Route::delete('/regions/destroySupplierRegion/{region}', [RegionController::class, 'destroySupplierRegion'])->name('region.destroySupplierRegion');

    //supplier

    Route::prefix('supplier')->group(function () {
        Route::controller(ProductController::class)->group(function () {
            Route::get('products', 'index')->name('products.index');
            Route::get('products/create', 'create')->name('products.create');
            Route::post('products/store', 'store')->name('products.store');
            Route::get('products/show/{id}', 'show')->name('products.show');
            Route::get('products/edit/{id}', 'edit')->name('products.edit');
            Route::put('products/update/{id}', 'update')->name('products.update');
            Route::delete('products/delete/{id}', 'destroy')->name('products.destroy');

            Route::get('/orders', [SupplierOrdersController::class, 'index'])->name('supplier.orders.index');
            Route::post('/orders/{order}/update-status', [SupplierOrdersController::class, 'updateStatus'])->name('supplier.orders.updateStatus');

            Route::put('/orders/{order}/change-status', [SupplierOrdersController::class, 'changeStatus'])
                ->name('supplier.orders.changeStatus');

            Route::post('/orders/{order}/cancel', [SupplierOrdersController::class, 'cancel'])
                ->name('supplier.orders.cancel');
        });
    });
    Route::post('products/store', [ProductController::class, 'store'])->name('products.store');
    Route::get('supplier/regions', [RegionController::class, 'supplier_regions'])->name('regions.suppliers');
    Route::put('supplier/regions', [RegionController::class, 'updateRegions'])->name('regions.updateRegions');
    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{product}', [ProductController::class, 'show']);
    Route::get('orders/create', [ProductController::class, 'create']);


    Route::get('/productsForCustomer', [CustomerController::class, 'productsForCustomer'])->name('products.productsForCustomer');
    Route::get('products/supplier/{supplierId}', [CustomerController::class, 'productsBySupplier'])->name('products.productsBySupplier');
    Route::get('products/show/{id}', [CustomerController::class, 'showProduct'])->name('products.showProduct');


    Route::get('Suppliers/{supplier}', [SupplierController::class, 'show']);
    Route::get('Supplier/Products/{supplier}', [SupplierController::class, 'showSupplierProducts'])->name('supplier.products');

    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/index', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/update', [CartController::class, 'update']);
    Route::get('/cart/items', [CartController::class, 'items'])->name('cart.items');

    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [OrderController::class, 'show']);
    Route::get('orders/create', [OrderController::class, 'create']);
});

require __DIR__ . '/auth.php';
