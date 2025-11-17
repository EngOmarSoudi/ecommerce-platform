<?php

use App\Http\Controllers\Api\HealthCheckController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('api')->get('/health', [HealthCheckController::class, 'index']);

Route::prefix('v1')->group(function () {
    Route::get('/categories', function () {
        return response()->json([
            'data' => \App\Models\Category::all()
        ]);
    });
    
    Route::get('/products', function () {
        return response()->json([
            'data' => \App\Models\Product::with('skus')->get()
        ]);
    });
});
