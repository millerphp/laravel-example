<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $json = File::get(database_path('data/categories.json'));
        $data = json_decode($json, true);

        foreach ($data['categories'] as $topCategory) {
            // Create top-level category
            $top = Category::factory()->create([
                'name' => $topCategory['name'],
                'slug' => Str::slug($topCategory['name']),
                'parent_id' => null
            ]);

            foreach ($topCategory['subcategories'] as $secondLevel) {
                // Create second-level category
                $second = Category::factory()->create([
                    'name' => $secondLevel['name'],
                    'slug' => Str::slug($secondLevel['name']),
                    'parent_id' => $top->id
                ]);

                foreach ($secondLevel['subcategories'] as $thirdLevel) {
                    // Create third-level category
                    Category::factory()->create([
                        'name' => $thirdLevel['name'],
                        'slug' => Str::slug($thirdLevel['name']),
                        'parent_id' => $second->id
                    ]);
                }
            }
        }
    }
} 