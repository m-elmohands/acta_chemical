<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'parent_id' => null,
            'name_en' => Str::title($name),
            'name_ar' => 'فئة '.fake()->unique()->numerify('####'),
            'slug' => Str::slug($name).'-'.fake()->unique()->numerify('####'),
            'description_en' => fake()->optional()->sentence(),
            'description_ar' => null,
            'is_active' => true,
            'sort_order' => 0,
        ];
    }
}
