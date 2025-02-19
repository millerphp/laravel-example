<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;

class RolesAndUsersSeeder extends Seeder
{
    private const USERS = [
        'Administrator' => 'admin@example.com',
        'User' => 'user@example.com',
    ];

    public function run(): void
    {
        Role::factory()->createMany(
            collect(self::USERS)->keys()->map(fn (string $role) => ['name' => $role])
        );

        foreach (self::USERS as $role => $email) {
            User::factory()
                ->create([
                    'name' => $role,
                    'email' => $email,
                    'password' => 'password',
                ])
                ->assignRole($role);
        }

        $adminUser = User::where('name', 'Administrator')->first();
        $user = User::where('name', 'User')->first();

        $adminUser->assignRole('administrator');
        $user->assignRole('customer');
    }
}
