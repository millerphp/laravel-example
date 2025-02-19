<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\ProductStock;
use App\Models\StockMovement;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    // Product variations for generating more realistic products
    private array $colors = ['Black', 'White', 'Silver', 'Gold', 'Blue', 'Red', 'Green', 'Purple', 'Pink', 'Gray'];
    private array $sizes = ['Small', 'Medium', 'Large', 'XL', 'XXL', '128GB', '256GB', '512GB', '1TB'];
    private array $brands = ['Samsung', 'Apple', 'Sony', 'LG', 'Nike', 'Adidas', 'Dell', 'HP', 'Asus', 'Lenovo'];
    
    private array $usedTitles = [];

    // Add image collections for different categories
    private array $imageCollections = [
        'electronics' => [
            'https://images.unsplash.com/photo-1526738549149-8e07eca6c147',
            'https://images.unsplash.com/photo-1505740420928-5e560c06d30e',
            // Add more electronics images...
        ],
        'clothing' => [
            'https://images.unsplash.com/photo-1523381210434-271e8be1f52b',
            'https://images.unsplash.com/photo-1542060748-10c28b62716f',
            // Add more clothing images...
        ],
        'computers' => [
            'https://images.unsplash.com/photo-1517694712202-14dd9538aa97',
            'https://images.unsplash.com/photo-1496181133206-80ce9b88a853',
            // Add more computer images...
        ],
        'phones' => [
            'https://images.unsplash.com/photo-1510557880182-3d4d3cba35a5',
            'https://images.unsplash.com/photo-1592899677977-9c10ca588bbd',
            // Add more phone images...
        ],
        'default' => [
            'https://images.unsplash.com/photo-1523275335684-37898b6baf30',
            'https://images.unsplash.com/photo-1505740420928-5e560c06d30e',
            // Add more generic product images...
        ]
    ];

    private function getRandomImage(string $category): string
    {
        $collection = $this->imageCollections[strtolower($category)] 
            ?? $this->imageCollections['default'];
        
        return $collection[array_rand($collection)] . '?w=600&h=600&fit=crop';
    }

    public function run(): void
    {
        $json = File::get(database_path('data/products.json'));
        $data = json_decode($json, true);
        
        // First create our base products
        foreach ($data['products'] as $productData) {
            $this->createProduct($this->ensureUniqueTitle($productData));
        }

        // Now let's generate variations and additional products for each category
        $categories = Category::all();
        
        foreach ($categories as $category) {
            // Generate between 10-20 products for each category
            $count = rand(10, 20);
            
            for ($i = 0; $i < $count; $i++) {
                $productData = $this->generateProductForCategory($category);
                $this->createProduct($this->ensureUniqueTitle($productData));
            }
        }
    }

    private function ensureUniqueTitle(array $productData): array
    {
        $originalTitle = $productData['title'];
        $title = $originalTitle;
        $counter = 1;

        while (in_array($title, $this->usedTitles)) {
            $counter++;
            $title = $originalTitle . ' ' . $counter;
        }

        $this->usedTitles[] = $title;
        $productData['title'] = $title;

        return $productData;
    }

    private function createProduct(array $productData): void
    {
        $mainCategory = Category::where('name', $productData['categories'][0])->first();
        
        $product = Product::factory()->create([
            'title' => $productData['title'],
            'slug' => Str::slug($productData['title']),
            'description' => $productData['description'],
            'price' => $productData['price'],
            'stock' => $productData['stock'] ?? rand(5, 200),
            'is_active' => true,
            'image' => $this->getRandomImage($mainCategory->name),
        ]);

        // Attach categories
        foreach ($productData['categories'] as $categoryName) {
            $category = Category::where('name', $categoryName)->first();
            if ($category) {
                $product->categories()->attach($category->id);
            }
        }

        $this->setupProductStock($product);
    }

    private function setupProductStock(Product $product): void
    {
        // Get all active warehouses
        $warehouses = Warehouse::where('is_active', true)->get();
        
        foreach ($warehouses as $warehouse) {
            // Determine if this product should be stocked in this warehouse
            if (fake()->boolean(80)) { // 80% chance of being stocked in each warehouse
                $stockLevel = match (true) {
                    $product->price >= 1000 => 'low', // Expensive items have lower stock
                    $product->price <= 50 => 'high',  // Cheaper items have higher stock
                    default => 'medium',
                };

                $quantity = match ($stockLevel) {
                    'low' => fake()->numberBetween(1, 20),
                    'medium' => fake()->numberBetween(20, 100),
                    'high' => fake()->numberBetween(100, 500),
                };

                ProductStock::factory()->create([
                    'product_id' => $product->id,
                    'warehouse_id' => $warehouse->id,
                    'quantity' => $quantity,
                    'minimum_quantity' => $quantity * 0.1,
                    'reorder_point' => $quantity * 0.2,
                ]);

                // Create some stock movements for history
                $this->createInitialStockMovements($product, $warehouse, $quantity);
            }
        }
    }

    private function createInitialStockMovements(Product $product, Warehouse $warehouse, int $quantity): void
    {
        // Create initial stock receipt
        StockMovement::factory()->create([
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'type' => 'receive',
            'quantity' => $quantity,
            'previous_quantity' => 0,
            'new_quantity' => $quantity,
            'notes' => 'Initial stock receipt',
        ]);

        // Create some random historical movements
        $movements = fake()->numberBetween(0, 5);
        for ($i = 0; $i < $movements; $i++) {
            $type = fake()->randomElement(['ship', 'receive', 'adjust']);
            $moveQuantity = match ($type) {
                'ship' => -fake()->numberBetween(1, 10),
                'receive' => fake()->numberBetween(5, 20),
                'adjust' => fake()->numberBetween(-5, 5),
            };

            StockMovement::factory()->create([
                'product_id' => $product->id,
                'warehouse_id' => $warehouse->id,
                'type' => $type,
                'quantity' => $moveQuantity,
                'previous_quantity' => $quantity,
                'new_quantity' => $quantity + $moveQuantity,
            ]);

            $quantity += $moveQuantity;
        }
    }

    private function generateProductForCategory(Category $category): array
    {
        $brand = $this->brands[array_rand($this->brands)];
        $color = $this->colors[array_rand($this->colors)];
        $size = $this->sizes[array_rand($this->sizes)];

        // Generate product based on category
        switch ($category->name) {
            case 'Smartphones':
            case 'Android Phones':
            case 'iPhones':
                return $this->generatePhoneProduct($brand, $size);
            
            case 'Laptops':
            case 'Computers':
                return $this->generateComputerProduct($brand);
            
            case "Men's Clothing":
            case "Women's Clothing":
            case 'Shirts':
            case 'Pants':
            case 'Dresses':
                return $this->generateClothingProduct($brand, $color, $size, $category);
            
            default:
                return $this->generateGenericProduct($brand, $category);
        }
    }

    private function generatePhoneProduct(string $brand, string $storage): array
    {
        $model = 'Model ' . rand(10, 99);
        return [
            'title' => "$brand $model Smartphone $storage",
            'description' => "Feature-packed smartphone from $brand with $storage storage, high-resolution display, and advanced camera system",
            'price' => rand(399, 1299) + 0.99,
            'stock' => rand(10, 100),
            'categories' => ['Smartphones', $brand === 'Apple' ? 'iPhones' : 'Android Phones']
        ];
    }

    private function generateComputerProduct(string $brand): array
    {
        $series = ['Pro', 'Elite', 'Ultra', 'Gaming', 'Business'][array_rand(['Pro', 'Elite', 'Ultra', 'Gaming', 'Business'])];
        $ram = [8, 16, 32, 64][array_rand([8, 16, 32, 64])];
        $storage = [256, 512, 1024, 2048][array_rand([256, 512, 1024, 2048])];
        
        return [
            'title' => "$brand $series Laptop {$ram}GB RAM {$storage}GB SSD",
            'description' => "High-performance laptop featuring {$ram}GB RAM, {$storage}GB SSD storage, and the latest processor technology",
            'price' => rand(699, 2499) + 0.99,
            'stock' => rand(5, 50),
            'categories' => ['Laptops', 'Computers']
        ];
    }

    private function generateClothingProduct(string $brand, string $color, string $size, Category $category): array
    {
        $types = [
            "Men's Clothing" => ['T-Shirt', 'Polo', 'Jacket', 'Sweater', 'Hoodie'],
            "Women's Clothing" => ['Blouse', 'Top', 'Cardigan', 'Sweater', 'Dress'],
            'Shirts' => ['T-Shirt', 'Polo', 'Dress Shirt', 'Casual Shirt', 'Sport Shirt'],
            'Pants' => ['Jeans', 'Chinos', 'Dress Pants', 'Cargo Pants', 'Shorts'],
            'Dresses' => ['Maxi Dress', 'Cocktail Dress', 'Summer Dress', 'Evening Dress', 'Casual Dress']
        ];

        $type = $types[$category->name] ?? ['Casual Wear', 'Formal Wear', 'Sports Wear', 'Outdoor Wear'];
        $type = $type[array_rand($type)];

        return [
            'title' => "$brand $color $type - $size",
            'description' => "Stylish $color $type from $brand. Available in $size. Perfect for any occasion.",
            'price' => rand(29, 199) + 0.99,
            'stock' => rand(20, 200),
            'categories' => [$category->name]
        ];
    }

    private function generateGenericProduct(string $brand, Category $category): array
    {
        $adjectives = ['Premium', 'Professional', 'Advanced', 'Essential', 'Deluxe', 'Classic', 'Modern'];
        $adj = $adjectives[array_rand($adjectives)];

        return [
            'title' => "$brand $adj " . Str::singular($category->name),
            'description' => "High-quality " . Str::singular($category->name) . " from $brand. Features premium materials and exceptional craftsmanship.",
            'price' => rand(49, 999) + 0.99,
            'stock' => rand(10, 150),
            'categories' => [$category->name]
        ];
    }
} 