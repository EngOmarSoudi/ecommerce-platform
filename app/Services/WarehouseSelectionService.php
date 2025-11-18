<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\StockLevel;

class WarehouseSelectionService
{
    /**
     * Select optimal warehouse based on shipping address and product origin
     */
    public function selectWarehouse(Product $product, Address $shippingAddress): ?Warehouse
    {
        // Get all warehouses with stock for this product
        $warehousesWithStock = StockLevel::where('sku_id', $product->skus->first()->id)
            ->where('available_quantity', '>', 0)
            ->with('warehouse')
            ->get()
            ->pluck('warehouse');

        if ($warehousesWithStock->isEmpty()) {
            return null;
        }

        // Strategy 1: Match by product origin
        if ($product->origin_country === $shippingAddress->country) {
            $localWarehouse = $warehousesWithStock->first(function ($warehouse) use ($shippingAddress) {
                return $warehouse->country === $shippingAddress->country;
            });

            if ($localWarehouse) {
                return $localWarehouse;
            }
        }

        // Strategy 2: Match by state/region
        $regionalWarehouse = $warehousesWithStock->first(function ($warehouse) use ($shippingAddress) {
            return $warehouse->state === $shippingAddress->state;
        });

        if ($regionalWarehouse) {
            return $regionalWarehouse;
        }

        // Strategy 3: Calculate distance (simplified - use lat/long in production)
        $closestWarehouse = $this->findClosestWarehouse($warehousesWithStock, $shippingAddress);

        return $closestWarehouse ?? $warehousesWithStock->first();
    }

    /**
     * Find closest warehouse by distance (simplified)
     */
    protected function findClosestWarehouse($warehouses, Address $address): ?Warehouse
    {
        // In production, use Haversine formula or Google Distance Matrix API
        // For now, return first warehouse in same country
        return $warehouses->first(function ($warehouse) use ($address) {
            return $warehouse->country === $address->country;
        });
    }
}
