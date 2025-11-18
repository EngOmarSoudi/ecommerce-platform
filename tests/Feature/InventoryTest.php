<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Models\StockMovement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_view_inventory_page()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->get('/inventory');
        
        $response->assertStatus(200);
        $response->assertViewIs('inventory.index');
    }

    /** @test */
    public function inventory_displays_all_products()
    {
        $user = User::factory()->create();
        $products = Product::factory()->count(5)->create();
        
        $response = $this->actingAs($user)->get('/inventory');
        
        foreach ($products as $product) {
            $response->assertSee($product->name);
        }
    }

    /** @test */
    public function user_can_adjust_stock_quantity()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock_quantity' => 100]);
        
        $response = $this->actingAs($user)->post('/inventory/adjust', [
            'product_id' => $product->id,
            'quantity' => 50,
            'reason' => 'Restock from supplier',
        ]);
        
        $response->assertRedirect('/inventory');
        
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 150,
        ]);
        
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'quantity' => 50,
            'type' => 'adjustment_in',
        ]);
    }

    /** @test */
    public function stock_adjustment_creates_audit_trail()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock_quantity' => 100]);
        
        $this->actingAs($user)->post('/inventory/adjust', [
            'product_id' => $product->id,
            'quantity' => -25,
            'reason' => 'Damaged items',
        ]);
        
        $movement = StockMovement::latest()->first();
        
        $this->assertEquals($product->id, $movement->product_id);
        $this->assertEquals(-25, $movement->quantity);
        $this->assertEquals('adjustment_out', $movement->type);
        $this->assertEquals(100, $movement->old_quantity);
        $this->assertEquals(75, $movement->new_quantity);
        $this->assertEquals($user->id, $movement->user_id);
    }

    /** @test */
    public function cannot_adjust_stock_below_zero()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock_quantity' => 10]);
        
        $response = $this->actingAs($user)->post('/inventory/adjust', [
            'product_id' => $product->id,
            'quantity' => -20,
            'reason' => 'Test',
        ]);
        
        $response->assertSessionHasErrors();
        
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 10, // Unchanged
        ]);
    }

    /** @test */
    public function low_stock_products_are_highlighted()
    {
        $user = User::factory()->create();
        
        $lowStockProduct = Product::factory()->create([
            'stock_quantity' => 5,
            'low_stock_threshold' => 10,
        ]);
        
        $normalProduct = Product::factory()->create([
            'stock_quantity' => 50,
            'low_stock_threshold' => 10,
        ]);
        
        $response = $this->actingAs($user)->get('/inventory');
        
        $response->assertSee('Low Stock');
        $response->assertSee($lowStockProduct->name);
    }

    /** @test */
    public function inventory_can_be_exported_to_csv()
    {
        $user = User::factory()->create();
        Product::factory()->count(3)->create();
        
        $response = $this->actingAs($user)->get('/inventory?export=csv');
        
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }
}
