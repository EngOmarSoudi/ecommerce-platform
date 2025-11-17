<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Sku;
use App\Models\Product;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Models\StockLevel;
use App\Models\StockMovement;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SkuInventoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_skus_with_products()
    {
        $product = Product::factory()->create([
            'name' => 'iPhone 15 Pro',
            'sku_base' => 'IPH15PRO',
        ]);

        $sku = Sku::factory()->create([
            'product_id' => $product->id,
            'sku' => 'IPH15PRO-128GB',
            'name' => 'iPhone 15 Pro 128GB',
            'price' => 999.99,
            'stock_quantity' => 50,
        ]);

        $this->assertDatabaseHas('skus', [
            'product_id' => $product->id,
            'sku' => 'IPH15PRO-128GB',
            'name' => 'iPhone 15 Pro 128GB',
            'price' => 999.99,
            'stock_quantity' => 50,
        ]);

        $this->assertEquals($sku->product->id, $product->id);
        $this->assertEquals($product->skus->first()->id, $sku->id);
    }

    /** @test */
    public function skus_can_have_multiple_units()
    {
        $sku = Sku::factory()->create();
        $pieceUnit = Unit::factory()->create(['name' => 'Piece']);
        $packUnit = Unit::factory()->create(['name' => 'Pack']);

        $sku->units()->attach($pieceUnit->id, [
            'quantity_per_unit' => 1,
            'price_modifier' => 0,
            'is_default' => true
        ]);

        $sku->units()->attach($packUnit->id, [
            'quantity_per_unit' => 10,
            'price_modifier' => -5.00,
            'is_default' => false
        ]);

        $this->assertEquals(2, $sku->units()->count());
        $this->assertTrue($sku->units->contains($pieceUnit));
        $this->assertTrue($sku->units->contains($packUnit));
    }

    /** @test */
    public function warehouses_can_track_stock_levels()
    {
        $warehouse = Warehouse::factory()->create([
            'name' => 'Main Warehouse',
            'code' => 'MAIN',
        ]);

        $sku = Sku::factory()->create([
            'sku' => 'IPH15PRO-128GB',
            'name' => 'iPhone 15 Pro 128GB',
        ]);

        $stockLevel = StockLevel::factory()->create([
            'warehouse_id' => $warehouse->id,
            'sku_id' => $sku->id,
            'quantity_on_hand' => 100,
            'quantity_reserved' => 10,
        ]);

        $this->assertDatabaseHas('stock_levels', [
            'warehouse_id' => $warehouse->id,
            'sku_id' => $sku->id,
            'quantity_on_hand' => 100,
            'quantity_reserved' => 10,
        ]);

        $this->assertEquals($stockLevel->warehouse->id, $warehouse->id);
        $this->assertEquals($stockLevel->sku->id, $sku->id);
        $this->assertEquals(90, $stockLevel->quantity_available); // 100 - 10
    }

    /** @test */
    public function warehouses_can_track_stock_movements()
    {
        $warehouse = Warehouse::factory()->create([
            'name' => 'Main Warehouse',
            'code' => 'MAIN',
        ]);

        $sku = Sku::factory()->create([
            'sku' => 'IPH15PRO-128GB',
            'name' => 'iPhone 15 Pro 128GB',
        ]);

        $movement = StockMovement::factory()->create([
            'warehouse_id' => $warehouse->id,
            'sku_id' => $sku->id,
            'movement_type' => 'in',
            'quantity' => 50,
            'reference_type' => 'purchase_order',
            'reference_id' => 12345,
        ]);

        $this->assertDatabaseHas('stock_movements', [
            'warehouse_id' => $warehouse->id,
            'sku_id' => $sku->id,
            'movement_type' => 'in',
            'quantity' => 50,
            'reference_type' => 'purchase_order',
            'reference_id' => 12345,
        ]);

        $this->assertEquals($movement->warehouse->id, $warehouse->id);
        $this->assertEquals($movement->sku->id, $sku->id);
    }
}