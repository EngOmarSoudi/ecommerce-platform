<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        $roles = Role::withCount('users')->get();
        $selectedRole = null;
        $permissionGroups = [];

        if ($request->has('role')) {
            $selectedRole = Role::with('permissions')->findOrFail($request->role);
            
            // Group permissions by module
            $permissionGroups = Permission::all()->groupBy(function($permission) {
                return explode('.', $permission->name)[0] ?? 'general';
            });
        } elseif ($roles->isNotEmpty()) {
            $selectedRole = $roles->first()->load('permissions');
            
            $permissionGroups = Permission::all()->groupBy(function($permission) {
                return explode('.', $permission->name)[0] ?? 'general';
            });
        }

        return view('permissions.index', compact('roles', 'selectedRole', 'permissionGroups'));
    }

    public function update(Request $request, $roleId)
    {
        $role = Role::findOrFail($roleId);
        
        $permissions = $request->input('permissions', []);
        $role->permissions()->sync($permissions);

        return redirect()
            ->route('permissions.index', ['role' => $roleId])
            ->with('success', 'Permissions updated successfully');
    }
}
