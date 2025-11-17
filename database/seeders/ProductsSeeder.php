<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Sku;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some categories and brands for our products
        $smartphonesCategory = Category::where('slug', 'smartphones')->first();
        $laptopsCategory = Category::where('slug', 'laptops')->first();
        $mensClothingCategory = Category::where('slug', 'mens-clothing')->first();
        $womensClothingCategory = Category::where('slug', 'womens-clothing')->first();
        $fictionCategory = Category::where('slug', 'fiction')->first();
        
        $appleBrand = Brand::where('slug', 'apple')->first();
        $samsungBrand = Brand::where('slug', 'samsung')->first();
        $nikeBrand = Brand::where('slug', 'nike')->first();
        $adidasBrand = Brand::where('slug', 'adidas')->first();
        $penguinBrand = Brand::where('slug', 'penguin-books')->first();

        // Create some units
        $pieceUnit = Unit::create([
            'name' => 'Piece',
            'symbol' => 'pcs',
            'description' => 'Individual piece',
            'conversion_factor' => 1.0000,
            'is_base_unit' => true,
            'is_active' => true,
        ]);

        $packUnit = Unit::create([
            'name' => 'Pack',
            'symbol' => 'pack',
            'description' => 'Pack of items',
            'conversion_factor' => 10.0000,
            'is_base_unit' => false,
            'is_active' => true,
        ]);

        $dozenUnit = Unit::create([
            'name' => 'Dozen',
            'symbol' => 'doz',
            'description' => 'Dozen items',
            'conversion_factor' => 12.0000,
            'is_base_unit' => false,
            'is_active' => true,
        ]);

        // Create products
        $products = [
            // Electronics
            [
                'name' => 'iPhone 15 Pro',
                'slug' => 'iphone-15-pro',
                'short_description' => 'Latest iPhone with advanced camera system',
                'description' => 'The iPhone 15 Pro features a stunning titanium design, powerful A17 Pro chip, and an advanced camera system for professional-quality photos and videos.',
                'brand_id' => $appleBrand->id,
                'category_id' => $smartphonesCategory->id,
                'sku_base' => 'IPH15PRO',
                'status' => 'published',
                'is_featured' => true,
                'is_active' => true,
                'average_rating' => 4.8,
                'review_count' => 124,
            ],
            [
                'name' => 'Samsung Galaxy S24',
                'slug' => 'samsung-galaxy-s24',
                'short_description' => 'Flagship Android smartphone',
                'description' => 'The Samsung Galaxy S24 delivers exceptional performance with its powerful processor, stunning display, and versatile camera system.',
                'brand_id' => $samsungBrand->id,
                'category_id' => $smartphonesCategory->id,
                'sku_base' => 'S24GALAXY',
                'status' => 'published',
                'is_featured' => true,
                'is_active' => true,
                'average_rating' => 4.6,
                'review_count' => 98,
            ],
            [
                'name' => 'MacBook Air M2',
                'slug' => 'macbook-air-m2',
                'short_description' => 'Ultra-thin laptop with M2 chip',
                'description' => 'The MacBook Air M2 combines portability with powerful performance. Featuring the revolutionary M2 chip, stunning Retina display, and all-day battery life.',
                'brand_id' => $appleBrand->id,
                'category_id' => $laptopsCategory->id,
                'sku_base' => 'MBAIRM2',
                'status' => 'published',
                'is_featured' => true,
                'is_active' => true,
                'average_rating' => 4.7,
                'review_count' => 87,
            ],
            
            // Clothing
            [
                'name' => 'Nike Air Max 270',
                'slug' => 'nike-air-max-270',
                'short_description' => 'Comfortable running shoes',
                'description' => 'Experience unparalleled comfort with Nike Air Max 270. Featuring a large Air unit in the heel for maximum cushioning and a sleek design.',
                'brand_id' => $nikeBrand->id,
                'category_id' => $mensClothingCategory->id,
                'sku_base' => 'NIKEAM270',
                'status' => 'published',
                'is_featured' => false,
                'is_active' => true,
                'average_rating' => 4.5,
                'review_count' => 65,
            ],
            [
                'name' => 'Adidas Ultraboost 22',
                'slug' => 'adidas-ultraboost-22',
                'short_description' => 'Premium running shoes',
                'description' => 'Adidas Ultraboost 22 offers exceptional energy return and comfort. Featuring Boost technology and a sleek design for both performance and style.',
                'brand_id' => $adidasBrand->id,
                'category_id' => $womensClothingCategory->id,
                'sku_base' => 'ADUB22',
                'status' => 'published',
                'is_featured' => false,
                'is_active' => true,
                'average_rating' => 4.4,
                'review_count' => 52,
            ],
            
            // Books
            [
                'name' => 'The Great Gatsby',
                'slug' => 'the-great-gatsby',
                'short_description' => 'Classic American novel',
                'description' => 'F. Scott Fitzgerald\'s classic novel of the Jazz Age, exploring themes of wealth, love, and the American Dream in the 1920s.',
                'brand_id' => $penguinBrand->id,
                'category_id' => $fictionCategory->id,
                'sku_base' => 'GATSBY',
                'status' => 'published',
                'is_featured' => false,
                'is_active' => true,
                'average_rating' => 4.2,
                'review_count' => 210,
            ],
        ];

        foreach ($products as $productData) {
            $product = Product::create($productData);
            
            // Create SKUs for each product
            $skus = [];
            if (str_contains($product->name, 'iPhone')) {
                $skus = [
                    [
                        'product_id' => $product->id,
                        'sku' => $product->sku_base . '-128GB',
                        'name' => $product->name . ' 128GB',
                        'description' => $product->description . ' With 128GB storage.',
                        'price' => 999.99,
                        'compare_at_price' => 1099.99,
                        'cost_price' => 750.00,
                        'stock_quantity' => 50,
                        'low_stock_threshold' => 5,
                        'weight' => 0.19,
                        'length' => 14.5,
                        'width' => 7.2,
                        'height' => 0.8,
                        'dimensions' => '14.5 x 7.2 x 0.8 cm',
                        'status' => 'active',
                        'is_trackable' => true,
                        'is_active' => true,
                    ],
                    [
                        'product_id' => $product->id,
                        'sku' => $product->sku_base . '-256GB',
                        'name' => $product->name . ' 256GB',
                        'description' => $product->description . ' With 256GB storage.',
                        'price' => 1099.99,
                        'compare_at_price' => 1199.99,
                        'cost_price' => 850.00,
                        'stock_quantity' => 30,
                        'low_stock_threshold' => 5,
                        'weight' => 0.19,
                        'length' => 14.5,
                        'width' => 7.2,
                        'height' => 0.8,
                        'dimensions' => '14.5 x 7.2 x 0.8 cm',
                        'status' => 'active',
                        'is_trackable' => true,
                        'is_active' => true,
                    ],
                    [
                        'product_id' => $product->id,
                        'sku' => $product->sku_base . '-512GB',
                        'name' => $product->name . ' 512GB',
                        'description' => $product->description . ' With 512GB storage.',
                        'price' => 1299.99,
                        'compare_at_price' => 1399.99,
                        'cost_price' => 950.00,
                        'stock_quantity' => 20,
                        'low_stock_threshold' => 3,
                        'weight' => 0.19,
                        'length' => 14.5,
                        'width' => 7.2,
                        'height' => 0.8,
                        'dimensions' => '14.5 x 7.2 x 0.8 cm',
                        'status' => 'active',
                        'is_trackable' => true,
                        'is_active' => true,
                    ],
                ];
            } elseif (str_contains($product->name, 'Samsung')) {
                $skus = [
                    [
                        'product_id' => $product->id,
                        'sku' => $product->sku_base . '-128GB',
                        'name' => $product->name . ' 128GB',
                        'description' => $product->description . ' With 128GB storage.',
                        'price' => 899.99,
                        'compare_at_price' => 999.99,
                        'cost_price' => 650.00,
                        'stock_quantity' => 40,
                        'low_stock_threshold' => 5,
                        'weight' => 0.20,
                        'length' => 15.0,
                        'width' => 7.5,
                        'height' => 0.8,
                        'dimensions' => '15.0 x 7.5 x 0.8 cm',
                        'status' => 'active',
                        'is_trackable' => true,
                        'is_active' => true,
                    ],
                    [
                        'product_id' => $product->id,
                        'sku' => $product->sku_base . '-256GB',
                        'name' => $product->name . ' 256GB',
                        'description' => $product->description . ' With 256GB storage.',
                        'price' => 999.99,
                        'compare_at_price' => 1099.99,
                        'cost_price' => 750.00,
                        'stock_quantity' => 35,
                        'low_stock_threshold' => 5,
                        'weight' => 0.20,
                        'length' => 15.0,
                        'width' => 7.5,
                        'height' => 0.8,
                        'dimensions' => '15.0 x 7.5 x 0.8 cm',
                        'status' => 'active',
                        'is_trackable' => true,
                        'is_active' => true,
                    ],
                ];
            } elseif (str_contains($product->name, 'MacBook')) {
                $skus = [
                    [
                        'product_id' => $product->id,
                        'sku' => $product->sku_base . '-256GB',
                        'name' => $product->name . ' 256GB',
                        'description' => $product->description . ' With 256GB SSD storage.',
                        'price' => 1199.99,
                        'compare_at_price' => 1299.99,
                        'cost_price' => 900.00,
                        'stock_quantity' => 25,
                        'low_stock_threshold' => 3,
                        'weight' => 1.24,
                        'length' => 30.4,
                        'width' => 21.5,
                        'height' => 1.2,
                        'dimensions' => '30.4 x 21.5 x 1.2 cm',
                        'status' => 'active',
                        'is_trackable' => true,
                        'is_active' => true,
                    ],
                    [
                        'product_id' => $product->id,
                        'sku' => $product->sku_base . '-512GB',
                        'name' => $product->name . ' 512GB',
                        'description' => $product->description . ' With 512GB SSD storage.',
                        'price' => 1399.99,
                        'compare_at_price' => 1499.99,
                        'cost_price' => 1050.00,
                        'stock_quantity' => 20,
                        'low_stock_threshold' => 2,
                        'weight' => 1.24,
                        'length' => 30.4,
                        'width' => 21.5,
                        'height' => 1.2,
                        'dimensions' => '30.4 x 21.5 x 1.2 cm',
                        'status' => 'active',
                        'is_trackable' => true,
                        'is_active' => true,
                    ],
                ];
            } elseif (str_contains($product->name, 'Nike') || str_contains($product->name, 'Adidas')) {
                // Shoes with different sizes
                $sizes = ['8', '9', '10', '11'];
                foreach ($sizes as $size) {
                    $skus[] = [
                        'product_id' => $product->id,
                        'sku' => $product->sku_base . '-SIZE' . $size,
                        'name' => $product->name . ' Size ' . $size,
                        'description' => $product->description . ' Size ' . $size . '.',
                        'price' => str_contains($product->name, 'Nike') ? 129.99 : 119.99,
                        'compare_at_price' => str_contains($product->name, 'Nike') ? 149.99 : 139.99,
                        'cost_price' => str_contains($product->name, 'Nike') ? 80.00 : 70.00,
                        'stock_quantity' => rand(10, 30),
                        'low_stock_threshold' => 3,
                        'weight' => 1.0,
                        'length' => 30.0,
                        'width' => 20.0,
                        'height' => 15.0,
                        'dimensions' => '30.0 x 20.0 x 15.0 cm',
                        'status' => 'active',
                        'is_trackable' => true,
                        'is_active' => true,
                    ];
                }
            } else {
                // Default SKU for other products
                $skus = [
                    [
                        'product_id' => $product->id,
                        'sku' => $product->sku_base,
                        'name' => $product->name,
                        'description' => $product->description,
                        'price' => rand(10, 50),
                        'compare_at_price' => rand(15, 70),
                        'cost_price' => rand(5, 30),
                        'stock_quantity' => rand(20, 100),
                        'low_stock_threshold' => 5,
                        'weight' => 0.5,
                        'length' => 20.0,
                        'width' => 15.0,
                        'height' => 2.0,
                        'dimensions' => '20.0 x 15.0 x 2.0 cm',
                        'status' => 'active',
                        'is_trackable' => true,
                        'is_active' => true,
                    ],
                ];
            }

            foreach ($skus as $skuData) {
                $sku = Sku::create($skuData);
                
                // Attach units to SKUs
                if (str_contains($product->name, 'iPhone') || str_contains($product->name, 'Samsung')) {
                    $sku->units()->attach($pieceUnit->id, [
                        'quantity_per_unit' => 1,
                        'price_modifier' => 0,
                        'is_default' => true
                    ]);
                } elseif (str_contains($product->name, 'MacBook')) {
                    $sku->units()->attach($pieceUnit->id, [
                        'quantity_per_unit' => 1,
                        'price_modifier' => 0,
                        'is_default' => true
                    ]);
                } elseif (str_contains($product->name, 'Nike') || str_contains($product->name, 'Adidas')) {
                    $sku->units()->attach($pieceUnit->id, [
                        'quantity_per_unit' => 1,
                        'price_modifier' => 0,
                        'is_default' => true
                    ]);
                } else {
                    $sku->units()->attach($pieceUnit->id, [
                        'quantity_per_unit' => 1,
                        'price_modifier' => 0,
                        'is_default' => true
                    ]);
                    
                    // Add pack unit for books
                    if (str_contains($product->name, 'Great Gatsby')) {
                        $sku->units()->attach($packUnit->id, [
                            'quantity_per_unit' => 10,
                            'price_modifier' => -5.00, // Discount for bulk purchase
                            'is_default' => false
                        ]);
                    }
                }
            }
        }
    }
}