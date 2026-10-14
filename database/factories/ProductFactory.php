<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->words(3, true) . ' Medis';

        return [
            'store_id' => Store::factory(),
            'category_id' => Category::inRandomOrder()->value('id') ?? Category::factory(),
            'code' => 'MED-' . strtoupper(fake()->bothify('???-###')),
            'name' => ucwords($name),
            'slug' => Str::slug($name) . '-' . fake()->unique()->numberBetween(1000, 9999),
            'description' => fake()->paragraphs(2, true),
            'price' => fake()->randomElement([45000, 75000, 150000, 250000, 480000, 850000, 1200000, 2500000]),
            'stock' => fake()->numberBetween(10, 80),
            'is_active' => true,
        ];
    }
}
