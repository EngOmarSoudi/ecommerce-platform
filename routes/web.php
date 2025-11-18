<?php

use Illuminate\Support\Facades\Route;

Route::get('/brand-guidelines', function () { return view('brand.guidelines'); })->name('brand.guidelines');


Route::get('/', function () {
    return view('landing');
})->name('landing');

Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');

// Product routes
use App\Http\Controllers\Web\ProductController;
use App\Http\Controllers\Web\CategoryController;
use App\Http\Controllers\Web\CartController;
use App\Http\Controllers\Web\CheckoutController;

Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index']);
// Checkout routes
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout/address', [CheckoutController::class, 'storeAddress'])->name('checkout.storeAddress');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');

// Cart routes
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/items', [CartController::class, 'addItem'])->name('cart.addItem');
Route::put('/cart/items/{cartItemId}', [CartController::class, 'updateItem'])->name('cart.updateItem');
Route::delete('/cart/items/{cartItemId}', [CartController::class, 'removeItem'])->name('cart.removeItem');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

// Category routes
Route::get('/categories/{slug}', [CategoryController::class, 'show'])->name('categories.show');
