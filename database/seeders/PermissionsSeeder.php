<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Dashboard
            'access_dashboard',
            // User Management
            'user_can_edit_own_profile',
            'access_user_management',
            // Currencies
            'access_currencies',
            // Reports
            'access_reports',
            // Activity Logs
            'access_activity_logs',
            // Settings
            'access_settings',
            // Payment Methods
            'access_payment_methods',
        ];

        foreach ($permissions as $permission) {
            Permission::create([
                'name' => $permission,
            ]);
        }

        $role = Role::create([
            'name' => 'Admin',
        ]);

        $role->givePermissionTo($permissions);
        $role->revokePermissionTo('access_user_management');
    }
}
