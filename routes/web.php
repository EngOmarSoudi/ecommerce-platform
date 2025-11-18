<?php

use Illuminate\Support\Facades\Route;

Route::get('/brand-guidelines.pdf', [\App\Http\Controllers\BrandGuidelinesController::class, 'pdf'])->name('brand.guidelines.pdf');


Route::get('/', [\App\Http\Controllers\Web\LandingController::class, 'index'])->name('landing');

// Auth UI routes
Route::get('/login', function(){ return view('auth.login'); })->name('auth.login');
Route::post('/login', [\App\Http\Controllers\Web\AuthController::class, 'login'])->name('auth.login.submit');
Route::get('/register', function(){ return view('auth.register'); })->name('auth.register');
Route::post('/register', [\App\Http\Controllers\Web\AuthController::class, 'register'])->name('auth.register.submit');
Route::get('/password/forgot', function(){ return view('auth.forgot'); })->name('auth.password.forgot');
Route::post('/password/email', [\App\Http\Controllers\Web\AuthController::class, 'sendResetLink'])->name('auth.password.email');
Route::get('/password/reset/{token}', function($token){ return view('auth.reset', compact('token')); })->name('auth.password.reset');
Route::post('/password/reset/{token}', [\App\Http\Controllers\Web\AuthController::class, 'reset'])->name('auth.password.reset.submit');

// Product routes
use App\Http\Controllers\Web\ProductController;
use App\Http\Controllers\Web\CategoryController;
use App\Http\Controllers\Web\CartController;
use App\Http\Controllers\Web\CheckoutController;

Route::get('/dashboard', [\App\Http\Controllers\Web\DashboardController::class, 'index'])->name('dashboard.index');
// Checkout routes
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout/address', [CheckoutController::class, 'storeAddress'])->name('checkout.storeAddress');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
Route::post('/products', [ProductController::class, 'store'])->name('products.store');
Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');
Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');

// Cart routes
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/items', [CartController::class, 'addItem'])->name('cart.addItem');
Route::put('/cart/items/{cartItemId}', [CartController::class, 'updateItem'])->name('cart.updateItem');
Route::delete('/cart/items/{cartItemId}', [CartController::class, 'removeItem'])->name('cart.removeItem');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

// Category routes
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
Route::get('/categories/{id}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');
Route::get('/categories/{slug}', [CategoryController::class, 'show'])->name('categories.show');

// POS routes
Route::get('/pos', [\App\Http\Controllers\Web\POSController::class, 'index'])->name('pos.index');
Route::get('/pos/receipt', [\App\Http\Controllers\Web\POSController::class, 'receipt'])->name('pos.receipt');
Route::post('/pos/order', [\App\Http\Controllers\Web\POSController::class, 'createOrder'])->name('pos.createOrder');

// Orders routes
Route::get('/orders', [\App\Http\Controllers\Web\OrderController::class, 'index'])->name('orders.index');
Route::get('/orders/{id}', [\App\Http\Controllers\Web\OrderController::class, 'show'])->name('orders.show');
Route::post('/orders/{id}/status', [\App\Http\Controllers\Web\OrderController::class, 'updateStatus'])->name('orders.updateStatus');

// Refunds routes
Route::get('/refunds', [\App\Http\Controllers\Web\RefundController::class, 'index'])->name('refunds.index');
Route::post('/refunds', [\App\Http\Controllers\Web\RefundController::class, 'store'])->name('refunds.store');
Route::get('/refunds/{id}/receipt', [\App\Http\Controllers\Web\RefundController::class, 'receipt'])->name('refunds.receipt');

// Inventory routes
Route::get('/inventory', [\App\Http\Controllers\Web\InventoryController::class, 'index'])->name('inventory.index');
Route::post('/inventory/adjust', [\App\Http\Controllers\Web\InventoryController::class, 'adjust'])->name('inventory.adjust');

// Reports routes
Route::get('/reports', [\App\Http\Controllers\Web\ReportController::class, 'index'])->name('reports.index');

// Settings routes
Route::get('/settings', [\App\Http\Controllers\Web\SettingsController::class, 'index'])->name('settings.index');
Route::put('/settings', [\App\Http\Controllers\Web\SettingsController::class, 'update'])->name('settings.update');
Route::post('/settings/test-email', [\App\Http\Controllers\Web\SettingsController::class, 'testEmail'])->name('settings.test-email');
Route::post('/settings/test-sms', [\App\Http\Controllers\Web\SettingsController::class, 'testSMS'])->name('settings.test-sms');

// Profile routes
Route::get('/profile', [\App\Http\Controllers\Web\ProfileController::class, 'index'])->name('profile.index');
Route::put('/profile', [\App\Http\Controllers\Web\ProfileController::class, 'update'])->name('profile.update');
Route::put('/profile/avatar', [\App\Http\Controllers\Web\ProfileController::class, 'updateAvatar'])->name('profile.update-avatar');
Route::put('/profile/password', [\App\Http\Controllers\Web\ProfileController::class, 'updatePassword'])->name('profile.update-password');

// Permissions routes
Route::get('/permissions', [\App\Http\Controllers\Web\PermissionController::class, 'index'])->name('permissions.index');
Route::put('/permissions/{role}', [\App\Http\Controllers\Web\PermissionController::class, 'update'])->name('permissions.update');

// UI Demo route
Route::get('/ui-demo', function() { return view('ui-demo'); })->name('ui.demo');
