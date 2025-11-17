<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create main categories
        $electronics = Category::create([
            'name' => 'Electronics',
            'slug' => 'electronics',
            'description' => 'Electronic devices and gadgets',
            'image' => 'categories/electronics.jpg',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $clothing = Category::create([
            'name' => 'Clothing',
            'slug' => 'clothing',
            'description' => 'Apparel and fashion items',
            'image' => 'categories/clothing.jpg',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $home = Category::create([
            'name' => 'Home & Garden',
            'slug' => 'home-garden',
            'description' => 'Home improvement and garden supplies',
            'image' => 'categories/home-garden.jpg',
            'is_active' => true,
            'sort_order' => 3,
        ]);

        $books = Category::create([
            'name' => 'Books',
            'slug' => 'books',
            'description' => 'Books and educational materials',
            'image' => 'categories/books.jpg',
            'is_active' => true,
            'sort_order' => 4,
        ]);

        // Create subcategories for Electronics
        Category::create([
            'name' => 'Smartphones',
            'slug' => 'smartphones',
            'description' => 'Mobile phones and smartphones',
            'image' => 'categories/smartphones.jpg',
            'parent_id' => $electronics->id,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Category::create([
            'name' => 'Laptops',
            'slug' => 'laptops',
            'description' => 'Laptops and notebooks',
            'image' => 'categories/laptops.jpg',
            'parent_id' => $electronics->id,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        Category::create([
            'name' => 'Tablets',
            'slug' => 'tablets',
            'description' => 'Tablets and e-readers',
            'image' => 'categories/tablets.jpg',
            'parent_id' => $electronics->id,
            'is_active' => true,
            'sort_order' => 3,
        ]);

        // Create subcategories for Clothing
        Category::create([
            'name' => 'Men\'s Clothing',
            'slug' => 'mens-clothing',
            'description' => 'Clothing for men',
            'image' => 'categories/mens-clothing.jpg',
            'parent_id' => $clothing->id,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Category::create([
            'name' => 'Women\'s Clothing',
            'slug' => 'womens-clothing',
            'description' => 'Clothing for women',
            'image' => 'categories/womens-clothing.jpg',
            'parent_id' => $clothing->id,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        Category::create([
            'name' => 'Children\'s Clothing',
            'slug' => 'childrens-clothing',
            'description' => 'Clothing for children',
            'image' => 'categories/childrens-clothing.jpg',
            'parent_id' => $clothing->id,
            'is_active' => true,
            'sort_order' => 3,
        ]);

        // Create subcategories for Home & Garden
        Category::create([
            'name' => 'Furniture',
            'slug' => 'furniture',
            'description' => 'Home furniture',
            'image' => 'categories/furniture.jpg',
            'parent_id' => $home->id,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Category::create([
            'name' => 'Kitchen & Dining',
            'slug' => 'kitchen-dining',
            'description' => 'Kitchen and dining supplies',
            'image' => 'categories/kitchen-dining.jpg',
            'parent_id' => $home->id,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        // Create subcategories for Books
        Category::create([
            'name' => 'Fiction',
            'slug' => 'fiction',
            'description' => 'Fiction books',
            'image' => 'categories/fiction.jpg',
            'parent_id' => $books->id,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Category::create([
            'name' => 'Non-Fiction',
            'slug' => 'non-fiction',
            'description' => 'Non-fiction books',
            'image' => 'categories/non-fiction.jpg',
            'parent_id' => $books->id,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        Category::create([
            'name' => 'Children\'s Books',
            'slug' => 'childrens-books',
            'description' => 'Books for children',
            'image' => 'categories/childrens-books.jpg',
            'parent_id' => $books->id,
            'is_active' => true,
            'sort_order' => 3,
        ]);
    }
}