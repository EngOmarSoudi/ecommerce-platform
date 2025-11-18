<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

/**
 * @OA\Info(
 *     title="E-commerce API",
 *     version="1.0.0",
 *     description="API for e-commerce platform"
 * )
 * 
 * @OA\Server(
 *     url="/api/v1",
 *     description="API Server"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 * 
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     required={"id", "name", "email"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *     @OA\Property(property="phone", type="string", example="+1234567890"),
 *     @OA\Property(property="provider", type="string", example="google"),
 *     @OA\Property(property="provider_id", type="string", example="123456789"),
 *     @OA\Property(property="avatar", type="string", example="https://example.com/avatar.jpg"),
 *     @OA\Property(property="email_verified_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z")
 * )
 * 
 * @OA\Schema(
 *     schema="Category",
 *     type="object",
 *     required={"id", "name"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Electronics"),
 *     @OA\Property(property="description", type="string", example="Electronic devices and accessories"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z")
 * )
 * 
 * @OA\Schema(
 *     schema="Product",
 *     type="object",
 *     required={"id", "name", "price"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="iPhone 15"),
 *     @OA\Property(property="description", type="string", example="Latest iPhone model"),
 *     @OA\Property(property="price", type="number", format="float", example=999.99),
 *     @OA\Property(property="compare_at_price", type="number", format="float", example=1099.99),
 *     @OA\Property(property="cost_per_item", type="number", format="float", example=599.99),
 *     @OA\Property(property="stock_quantity", type="integer", example=100),
 *     @OA\Property(property="sku", type="string", example="IPH15-BLK-128"),
 *     @OA\Property(property="barcode", type="string", example="123456789012"),
 *     @OA\Property(property="weight", type="number", format="float", example=1.5),
 *     @OA\Property(property="weight_unit", type="string", example="kg"),
 *     @OA\Property(property="height", type="number", format="float", example=15.0),
 *     @OA\Property(property="width", type="number", format="float", example=7.0),
 *     @OA\Property(property="depth", type="number", format="float", example=0.8),
 *     @OA\Property(property="dimension_unit", type="string", example="cm"),
 *     @OA\Property(property="slug", type="string", example="iphone-15"),
 *     @OA\Property(property="status", type="string", example="active"),
 *     @OA\Property(property="category_id", type="integer", example=1),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z")
 * )
 * 
 * @OA\Schema(
 *     schema="Pagination",
 *     type="object",
 *     @OA\Property(property="current_page", type="integer", example=1),
 *     @OA\Property(property="last_page", type="integer", example=10),
 *     @OA\Property(property="per_page", type="integer", example=15),
 *     @OA\Property(property="total", type="integer", example=150)
 * )
 */
class BaseController extends Controller
{
    //
}