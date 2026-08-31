<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'category_id' => Category::factory(),
            'sku' => fake()->unique()->bothify('SKU-####-????'),
            'name_en' => Str::title($name),
            'name_ar' => 'منتج '.fake()->unique()->numerify('####'),
            'slug' => Str::slug($name).'-'.fake()->unique()->numerify('####'),
            'short_description_en' => fake()->sentence(),
            'short_description_ar' => null,
            'description_en' => fake()->paragraph(),
            'description_ar' => null,
            'price' => fake()->randomFloat(2, 1, 10000),
            'sale_price' => null,
            'stock_quantity' => fake()->numberBetween(0, 500),
            'track_inventory' => true,
            'is_active' => true,
            'is_featured' => false,
            'sort_order' => 0,
        ];
    }
}
