<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PermissionsSeeder::class,
            RolesAndUsersSeeder::class,
            CategorySeeder::class,
            WarehouseSeeder::class,
            ProductSeeder::class,
            DiscountSeeder::class,
            UsersAndCartsSeeder::class,
        ]);
    }
}
