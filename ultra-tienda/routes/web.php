<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderDetailController;
use App\Http\Controllers\OrderPaymentController;
use App\Http\Controllers\RoleController;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('users', UserController::class);
Route::resource('products', ProductController::class);
Route::resource('categories', CategoryController::class);
Route::resource('orders', OrderController::class);
Route::resource('order-details', OrderDetailController::class);
Route::resource('order-payments', OrderPaymentController::class);
Route::resource('roles', RoleController::class);
