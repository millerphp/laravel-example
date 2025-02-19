<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    public function run(): void
    {
        // Create main warehouses
        $mainWarehouses = [
            ['name' => 'Main Distribution Center', 'code' => 'MDC'],
            ['name' => 'East Coast Fulfillment', 'code' => 'ECF'],
            ['name' => 'West Coast Fulfillment', 'code' => 'WCF'],
            ['name' => 'Central Storage', 'code' => 'CST'],
        ];

        foreach ($mainWarehouses as $warehouse) {
            Warehouse::factory()->create([
                'name' => $warehouse['name'],
                'code' => $warehouse['code'],
            ]);
        }

        // Create a few random additional warehouses
        Warehouse::factory()->count(2)->create();
    }
} 