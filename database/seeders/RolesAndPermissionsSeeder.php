<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $adminRole = Role::create([
            'name' => 'admin',
            'description' => 'Administrator with full access',
            'is_active' => true,
        ]);

        $sellerRole = Role::create([
            'name' => 'seller',
            'description' => 'Seller with access to seller dashboard',
            'is_active' => true,
        ]);

        $customerRole = Role::create([
            'name' => 'customer',
            'description' => 'Customer with access to storefront',
            'is_active' => true,
        ]);

        $supportRole = Role::create([
            'name' => 'support',
            'description' => 'Support staff with access to support tools',
            'is_active' => true,
        ]);

        // Create permissions
        $permissions = [
            // Admin permissions
            ['name' => 'manage-users', 'description' => 'Manage users', 'module' => 'User Management'],
            ['name' => 'manage-roles', 'description' => 'Manage roles', 'module' => 'User Management'],
            ['name' => 'manage-permissions', 'description' => 'Manage permissions', 'module' => 'User Management'],
            ['name' => 'view-analytics', 'description' => 'View analytics dashboard', 'module' => 'Analytics'],
            ['name' => 'manage-settings', 'description' => 'Manage system settings', 'module' => 'System'],

            // Seller permissions
            ['name' => 'manage-products', 'description' => 'Manage products', 'module' => 'Catalog'],
            ['name' => 'manage-inventory', 'description' => 'Manage inventory', 'module' => 'Inventory'],
            ['name' => 'view-orders', 'description' => 'View orders', 'module' => 'Orders'],
            ['name' => 'fulfill-orders', 'description' => 'Fulfill orders', 'module' => 'Orders'],
            ['name' => 'manage-store', 'description' => 'Manage store settings', 'module' => 'Store'],

            // Customer permissions
            ['name' => 'place-orders', 'description' => 'Place orders', 'module' => 'Orders'],
            ['name' => 'view-own-orders', 'description' => 'View own orders', 'module' => 'Orders'],
            ['name' => 'manage-cart', 'description' => 'Manage shopping cart', 'module' => 'Cart'],
            ['name' => 'leave-reviews', 'description' => 'Leave product reviews', 'module' => 'Reviews'],

            // Support permissions
            ['name' => 'view-all-orders', 'description' => 'View all orders', 'module' => 'Orders'],
            ['name' => 'manage-refunds', 'description' => 'Process refunds', 'module' => 'Orders'],
            ['name' => 'respond-to-tickets', 'description' => 'Respond to support tickets', 'module' => 'Support'],
        ];

        foreach ($permissions as $permissionData) {
            Permission::create($permissionData);
        }

        // Assign permissions to roles
        // Admin gets all permissions
        $adminRole->permissions()->sync(Permission::all());

        // Seller gets seller permissions
        $sellerPermissions = Permission::whereIn('name', [
            'manage-products',
            'manage-inventory',
            'view-orders',
            'fulfill-orders',
            'manage-store',
        ])->get();
        $sellerRole->permissions()->sync($sellerPermissions);

        // Customer gets customer permissions
        $customerPermissions = Permission::whereIn('name', [
            'place-orders',
            'view-own-orders',
            'manage-cart',
            'leave-reviews',
        ])->get();
        $customerRole->permissions()->sync($customerPermissions);

        // Support gets support permissions
        $supportPermissions = Permission::whereIn('name', [
            'view-all-orders',
            'manage-refunds',
            'respond-to-tickets',
        ])->get();
        $supportRole->permissions()->sync($supportPermissions);
    }
}