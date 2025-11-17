<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            [
                'name' => 'Apple',
                'slug' => 'apple',
                'description' => 'Innovative technology products',
                'logo' => 'brands/apple.png',
                'is_active' => true,
            ],
            [
                'name' => 'Samsung',
                'slug' => 'samsung',
                'description' => 'Electronics and appliances',
                'logo' => 'brands/samsung.png',
                'is_active' => true,
            ],
            [
                'name' => 'Nike',
                'slug' => 'nike',
                'description' => 'Sportswear and athletic footwear',
                'logo' => 'brands/nike.png',
                'is_active' => true,
            ],
            [
                'name' => 'Adidas',
                'slug' => 'adidas',
                'description' => 'Sportswear and athletic footwear',
                'logo' => 'brands/adidas.png',
                'is_active' => true,
            ],
            [
                'name' => 'Sony',
                'slug' => 'sony',
                'description' => 'Electronics and entertainment',
                'logo' => 'brands/sony.png',
                'is_active' => true,
            ],
            [
                'name' => 'LG',
                'slug' => 'lg',
                'description' => 'Electronics and home appliances',
                'logo' => 'brands/lg.png',
                'is_active' => true,
            ],
            [
                'name' => 'H&M',
                'slug' => 'hm',
                'description' => 'Fashion retailer',
                'logo' => 'brands/hm.png',
                'is_active' => true,
            ],
            [
                'name' => 'Zara',
                'slug' => 'zara',
                'description' => 'Fashion retailer',
                'logo' => 'brands/zara.png',
                'is_active' => true,
            ],
            [
                'name' => 'IKEA',
                'slug' => 'ikea',
                'description' => 'Furniture and home goods',
                'logo' => 'brands/ikea.png',
                'is_active' => true,
            ],
            [
                'name' => 'Penguin Books',
                'slug' => 'penguin-books',
                'description' => 'Publishing house',
                'logo' => 'brands/penguin-books.png',
                'is_active' => true,
            ],
        ];

        foreach ($brands as $brandData) {
            Brand::create($brandData);
        }
    }
}