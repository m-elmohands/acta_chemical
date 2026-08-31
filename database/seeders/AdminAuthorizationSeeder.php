<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class AdminAuthorizationSeeder extends Seeder
{
    public const PERMISSIONS = [
        'view categories', 'manage categories',
        'view products', 'manage products',
        'view orders', 'manage orders',
        'view payments', 'manage payments',
        'view customers', 'manage customers',
        'view ecommerce dashboard',
    ];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = collect(self::PERMISSIONS)->map(
            fn (string $name) => Permission::findOrCreate($name, 'web')
        );

        $role = Role::findOrCreate('super-admin', 'web');
        $role->syncPermissions($permissions);

        User::where('email', 'admin@gmail.com')->first()?->assignRole($role);
    }
}
