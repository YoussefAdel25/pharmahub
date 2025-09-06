<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\homeController;
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

require __DIR__ . '/auth.php';

Route::middleware('auth')->group(function () {

    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('profile.show');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/update', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/destroy', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });


    Route::get('/', [homeController::class, 'index'])->name('dashboard');


    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('products.index');
        Route::get('/by-region', [ProductController::class, 'getByRegion']);
        Route::get('/supplier/{supplierId}', [CustomerController::class, 'productsBySupplier'])->name('products.productsBySupplier');
        Route::get('/show/{id}', [CustomerController::class, 'showProduct'])->name('products.showProduct');
    });


    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/create', [OrderController::class, 'create']);
        Route::get('/{order}', [OrderController::class, 'show']);
        Route::post('/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    });


    Route::prefix('cart')->group(function () {
        Route::post('/add', [CartController::class, 'add'])->name('cart.add');
        Route::post('/index', [CartController::class, 'index'])->name('cart.index');
        Route::post('/update', [CartController::class, 'update']);
        Route::get('/items', [CartController::class, 'items'])->name('cart.items');
        Route::post('/checkout', [CheckoutController::class, 'checkout'])->name('cart.checkout');
    });


    Route::prefix('regions')->group(function () {
        Route::get('/', [RegionController::class, 'index'])->name('region.index');
        Route::get('/create', [RegionController::class, 'create'])->name('region.create');
        Route::post('/store', [RegionController::class, 'store'])->name('region.store');
        Route::get('/edit/{region}', [RegionController::class, 'edit'])->name('region.edit');
        Route::put('/update/{region}', [RegionController::class, 'update'])->name('region.update');
        Route::delete('/delete/{region}', [RegionController::class, 'destroy'])->name('region.destroy');
        Route::delete('/destroySupplierRegion/{region}', [RegionController::class, 'destroySupplierRegion'])->name('region.destroySupplierRegion');
        Route::get('/suppliers', [RegionController::class, 'supplier_regions'])->name('regions.suppliers');
        Route::put('/suppliers/update', [RegionController::class, 'updateRegions'])->name('regions.updateRegions');
    });


    Route::prefix('supplier')->group(function () {

        Route::prefix('products')->controller(ProductController::class)->group(function () {
            Route::get('/', 'index')->name('supplier.products.index');
            Route::get('/create', 'create')->name('supplier.products.create');
            Route::post('/store', 'store')->name('supplier.products.store');
            Route::get('/show/{id}', 'show')->name('supplier.products.show');
            Route::get('/edit/{id}', 'edit')->name('supplier.products.edit');
            Route::put('/update/{id}', 'update')->name('supplier.products.update');
            Route::delete('/delete/{id}', 'destroy')->name('supplier.products.destroy');
        });

        Route::prefix('orders')->controller(SupplierOrdersController::class)->group(function () {
            Route::get('/', 'index')->name('supplier.orders.index');
            Route::post('/{order}/update-status', 'updateStatus')->name('supplier.orders.updateStatus');
            Route::put('/{order}/change-status', 'changeStatus')->name('supplier.orders.changeStatus');
            Route::post('/{order}/cancel', 'cancel')->name('supplier.orders.cancel');
        });
    });

    Route::prefix('admin')->group(function () {

        // Users Management
        Route::prefix('users')->controller(UserController::class)->group(function () {
            Route::get('/', 'index')->name('users.index');
            Route::get('/create', 'create')->name('users.create');
            Route::post('/store', 'store')->name('users.store');
            Route::get('/edit/{user}', 'edit')->name('users.edit');
            Route::put('/update/{user}', 'update')->name('users.update');
            Route::delete('/{user}', 'destroy')->name('users.destroy');
            Route::post('/bulk-delete', 'bulkDelete')->name('users.bulkDelete');
        });

        Route::get('/products', [ProductController::class, 'allProducts'])->name('allProducts.index');

        Route::get('/orders', [OrderController::class, 'allOrders'])->name('allOrders.index');
    });


    Route::get('/productsForCustomer', [CustomerController::class, 'productsForCustomer'])->name('products.productsForCustomer');


    Route::get('/Suppliers/{supplier}', [SupplierController::class, 'show']);
    Route::get('/Supplier/Products/{supplier}', [SupplierController::class, 'showSupplierProducts'])->name('supplier.products');
});
