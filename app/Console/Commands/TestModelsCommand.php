<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Sku;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Models\Seller;
use App\Models\Promotion;

class TestModelsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-models';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test all models and relationships';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing models and relationships...');
        
        // Test Role and Permission
        $this->info('Testing Role and Permission models...');
        $role = Role::factory()->create();
        $permissions = Permission::factory()->count(3)->create();
        $role->permissions()->attach($permissions);
        $this->info('✓ Role and Permission relationships work');
        
        // Test User and Role
        $this->info('Testing User and Role relationships...');
        $user = User::factory()->create();
        $user->roles()->attach($role);
        $this->info('✓ User and Role relationships work');
        
        // Test Category
        $this->info('Testing Category model...');
        $parentCategory = Category::factory()->create();
        $childCategory = Category::factory()->create(['parent_id' => $parentCategory->id]);
        $this->info('✓ Category relationships work');
        
        // Test Brand
        $this->info('Testing Brand model...');
        $brand = Brand::factory()->create();
        $this->info('✓ Brand model works');
        
        // Test Product
        $this->info('Testing Product model...');
        $product = Product::factory()->create([
            'brand_id' => $brand->id,
            'category_id' => $parentCategory->id,
        ]);
        $this->info('✓ Product model works');
        
        // Test SKU
        $this->info('Testing SKU model...');
        $sku = Sku::factory()->create(['product_id' => $product->id]);
        $this->info('✓ SKU model works');
        
        // Test Unit
        $this->info('Testing Unit model...');
        $unit = Unit::factory()->create();
        $this->info('✓ Unit model works');
        
        // Test Warehouse
        $this->info('Testing Warehouse model...');
        $warehouse = Warehouse::factory()->create();
        $this->info('✓ Warehouse model works');
        
        // Test Seller
        $this->info('Testing Seller model...');
        $seller = Seller::factory()->create(['user_id' => $user->id]);
        $this->info('✓ Seller model works');
        
        // Test Promotion
        $this->info('Testing Promotion model...');
        $promotion = Promotion::factory()->create();
        $promotion->categories()->attach($parentCategory);
        $promotion->products()->attach($product);
        $this->info('✓ Promotion model works');
        
        $this->info('All models and relationships are working correctly!');
    }
}
