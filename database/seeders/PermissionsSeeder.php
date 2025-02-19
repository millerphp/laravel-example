<?php

namespace Database\Seeders;

use App\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $json = File::get(database_path('data/permissions.json'));
        $config = json_decode($json, true);

        // Create all permissions
        foreach ($config['permissions'] as $name => $description) {
            Permission::factory()->create([
                'name' => $name,
                'guard_name' => 'web',
            ]);
        }

        // Create roles and assign permissions
        foreach ($config['roles'] as $roleKey => $roleData) {
            $role = Role::create([
                'name' => $roleKey,
                'guard_name' => 'web',
            ]);

            // If permissions is "*", give all permissions
            if ($roleData['permissions'] === '*') {
                $role->givePermissionTo(Permission::all());
            } else {
                $role->givePermissionTo($roleData['permissions']);
            }
        }
    }
} 