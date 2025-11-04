<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionTableSeeder extends Seeder
{
    public function run(): void
    {
        $rolePermissions = [
            'admin' => Permission::all()->pluck('name')->all(),
            'staff' => ['manage_products', 'manage_contacts'],
            'delivery_staff' => ['manage_deliveries'],
        ];

        foreach ($rolePermissions as $roleName => $permissionNames) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $permissions = Permission::whereIn('name', $permissionNames)->pluck('id')->all();

            if ($permissions) {
                $role->permissions()->sync($permissions);
            } else {
                $this->command?->warn("No permissions found for role {$roleName}.");
            }
        }
    }
}