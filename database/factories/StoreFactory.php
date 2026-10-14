<?php

namespace Database\Factories;

use App\Enums\StoreStatus;
use App\Models\City;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Store>
 */
class StoreFactory extends Factory
{
    protected $model = Store::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->company() . ' Alkes';

        return [
            'owner_user_id' => User::factory()->vendor(),
            'city_id' => City::inRandomOrder()->value('id') ?? City::factory(),
            'name' => $name,
            'slug' => Str::slug($name) . '-' . fake()->unique()->numberBetween(100, 999),
            'description' => fake()->paragraph(),
            'status' => StoreStatus::ACTIVE,
            'logo_path' => null,
            'signature_path' => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => StoreStatus::PENDING,
        ]);
    }
}
