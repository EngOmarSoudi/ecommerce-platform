<?php

use App\Http\Controllers\Web\CartController;
use App\Http\Controllers\Web\CategoryController;
use App\Http\Controllers\Web\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Product routes
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

// Category routes
Route::get('/categories/{slug}', [CategoryController::class, 'show'])->name('categories.show');

// Cart routes
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/items', [CartController::class, 'addItem'])->name('cart.addItem');
Route::put('/cart/items/{cartItemId}', [CartController::class, 'updateItem'])->name('cart.updateItem');
Route::delete('/cart/items/{cartItemId}', [CartController::class, 'removeItem'])->name('cart.removeItem');
Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');
