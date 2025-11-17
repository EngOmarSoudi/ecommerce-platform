<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RolePermissionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_roles_and_permissions()
    {
        $role = Role::factory()->create([
            'name' => 'admin',
            'description' => 'Administrator role',
        ]);

        $permission = Permission::factory()->create([
            'name' => 'manage-users',
            'description' => 'Can manage users',
            'module' => 'User Management',
        ]);

        $this->assertDatabaseHas('roles', [
            'name' => 'admin',
            'description' => 'Administrator role',
        ]);

        $this->assertDatabaseHas('permissions', [
            'name' => 'manage-users',
            'description' => 'Can manage users',
            'module' => 'User Management',
        ]);
    }

    /** @test */
    public function roles_can_have_permissions()
    {
        $role = Role::factory()->create(['name' => 'admin']);
        $permission = Permission::factory()->create(['name' => 'manage-users']);

        $role->permissions()->attach($permission);

        $this->assertTrue($role->permissions->contains($permission));
        $this->assertEquals(1, $role->permissions()->count());
    }

    /** @test */
    public function users_can_have_roles()
    {
        $user = User::factory()->create();
        $role = Role::factory()->create(['name' => 'admin']);

        $user->roles()->attach($role);

        $this->assertTrue($user->roles->contains($role));
        $this->assertEquals(1, $user->roles()->count());
    }

    /** @test */
    public function users_can_check_roles()
    {
        $user = User::factory()->create();
        $adminRole = Role::factory()->create(['name' => 'admin']);
        $customerRole = Role::factory()->create(['name' => 'customer']);

        $user->roles()->attach($adminRole);

        $this->assertTrue($user->hasRole('admin'));
        $this->assertFalse($user->hasRole('customer'));
        $this->assertTrue($user->hasRole($adminRole));
        $this->assertFalse($user->hasRole($customerRole));
    }
}