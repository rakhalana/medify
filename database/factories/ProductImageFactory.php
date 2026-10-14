<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductImage>
 */
class ProductImageFactory extends Factory
{
    protected $model = ProductImage::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'path' => 'products/sample-' . fake()->numberBetween(1, 10) . '.jpg',
            'thumb_path' => 'products/thumbnails/sample-' . fake()->numberBetween(1, 10) . '.jpg',
            'sort_order' => 0,
        ];
    }
}
