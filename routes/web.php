<?php

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
