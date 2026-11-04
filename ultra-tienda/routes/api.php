<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\OrderDetailController;
use App\Http\Controllers\Api\OrderPaymentController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\Storefront\CategoryController as StorefrontCategoryController;
use App\Http\Controllers\Api\Storefront\OrderController as StorefrontOrderController;
use App\Http\Controllers\Api\Storefront\ProductController as StorefrontProductController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:10,1')
    ->name('api.login');

/*
|--------------------------------------------------------------------------
| Tienda pública
|--------------------------------------------------------------------------
|
| Endpoints sin autenticación que consume el frontend de la tienda: catálogo
| activo y creación de pedidos. El precio y el total siempre se calculan en
| el servidor con los datos de la base de datos.
|
*/
Route::prefix('store')->name('api.store.')->group(function () {
    Route::get('/categories', [StorefrontCategoryController::class, 'index'])->name('categories.index');
    Route::get('/products', [StorefrontProductController::class, 'index'])->name('products.index');
    Route::get('/products/{product}', [StorefrontProductController::class, 'show'])->name('products.show');
    Route::post('/orders', [StorefrontOrderController::class, 'store'])
        ->middleware('throttle:20,1')
        ->name('orders.store');
});

Route::middleware('auth:sanctum')->name('api.')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/me', [AuthController::class, 'me'])->name('me');

    Route::apiResource('roles', RoleController::class);
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('products', ProductController::class);
    Route::apiResource('users', UserController::class);
    Route::apiResource('orders', OrderController::class);
    Route::apiResource('order-details', OrderDetailController::class);
    Route::apiResource('order-payments', OrderPaymentController::class);
});
