<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Seller;
use App\Models\User;
use App\Models\SellerStore;
use App\Models\SellerDocument;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SellerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_sellers_with_users()
    {
        $user = User::factory()->create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
        ]);

        $seller = Seller::factory()->create([
            'user_id' => $user->id,
            'company_name' => 'Tech Solutions Inc.',
            'business_license_number' => 'BL-123456',
            'tax_id' => 'TAX-789012',
            'status' => 'approved',
        ]);

        $this->assertDatabaseHas('sellers', [
            'user_id' => $user->id,
            'company_name' => 'Tech Solutions Inc.',
            'business_license_number' => 'BL-123456',
            'tax_id' => 'TAX-789012',
            'status' => 'approved',
        ]);

        $this->assertEquals($seller->user->id, $user->id);
        $this->assertEquals($user->seller->id, $seller->id);
    }

    /** @test */
    public function sellers_can_have_stores()
    {
        $seller = Seller::factory()->create([
            'company_name' => 'Tech Solutions Inc.',
        ]);

        $store = SellerStore::factory()->create([
            'seller_id' => $seller->id,
            'store_name' => 'Tech Solutions Store',
            'store_slug' => 'tech-solutions',
            'description' => 'Your one-stop tech shop',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('seller_stores', [
            'seller_id' => $seller->id,
            'store_name' => 'Tech Solutions Store',
            'store_slug' => 'tech-solutions',
            'is_active' => true,
        ]);

        $this->assertEquals($store->seller->id, $seller->id);
        $this->assertEquals($seller->store->id, $store->id);
    }

    /** @test */
    public function sellers_can_have_documents()
    {
        $seller = Seller::factory()->create([
            'company_name' => 'Tech Solutions Inc.',
        ]);

        $document1 = SellerDocument::factory()->create([
            'seller_id' => $seller->id,
            'document_type' => 'business_license',
            'file_path' => 'documents/business_license.pdf',
            'is_verified' => true,
        ]);

        $document2 = SellerDocument::factory()->create([
            'seller_id' => $seller->id,
            'document_type' => 'tax_id',
            'file_path' => 'documents/tax_id.pdf',
            'is_verified' => false,
        ]);

        $this->assertEquals(2, $seller->documents()->count());
        $this->assertTrue($seller->documents->contains($document1));
        $this->assertTrue($seller->documents->contains($document2));
    }

    /** @test */
    public function sellers_can_have_products()
    {
        $seller = Seller::factory()->create([
            'company_name' => 'Tech Solutions Inc.',
        ]);

        $product1 = Product::factory()->create([
            'name' => 'Smartphone X1',
            'seller_id' => $seller->id,
        ]);

        $product2 = Product::factory()->create([
            'name' => 'Laptop Pro',
            'seller_id' => $seller->id,
        ]);

        $this->assertEquals(2, $seller->products()->count());
        $this->assertTrue($seller->products->contains($product1));
        $this->assertTrue($seller->products->contains($product2));
        $this->assertEquals($product1->seller->id, $seller->id);
    }
}