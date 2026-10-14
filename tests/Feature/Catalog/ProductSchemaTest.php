<?php

namespace Tests\Feature\Catalog;

use App\Enums\StoreStatus;
use App\Models\Category;
use App\Models\City;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_can_be_created_with_owner_and_city(): void
    {
        $city = City::factory()->create(['name' => 'Surabaya']);
        $vendor = User::factory()->vendor()->create(['city_id' => $city->id]);

        $store = Store::create([
            'owner_user_id' => $vendor->id,
            'city_id' => $city->id,
            'name' => 'Toko Alkes Medika',
            'slug' => 'toko-alkes-medika',
            'description' => 'Toko resmi alat kesehatan.',
            'status' => StoreStatus::ACTIVE,
        ]);

        $this->assertDatabaseHas('stores', [
            'id' => $store->id,
            'name' => 'Toko Alkes Medika',
            'status' => 'active',
        ]);

        $this->assertEquals($vendor->id, $store->owner->id);
        $this->assertEquals($city->id, $store->city->id);
        $this->assertTrue($store->isActive());
        $this->assertEquals($store->id, $vendor->fresh()->store->id);
    }

    public function test_product_belongs_to_store_and_category_with_images(): void
    {
        $city = City::factory()->create();
        $vendor = User::factory()->vendor()->create(['city_id' => $city->id]);
        $store = Store::factory()->create([
            'owner_user_id' => $vendor->id,
            'city_id' => $city->id,
        ]);
        $category = Category::factory()->create(['name' => 'Diagnostik']);

        $product = Product::create([
            'store_id' => $store->id,
            'category_id' => $category->id,
            'code' => 'MED-TNS-999',
            'name' => 'Tensimeter Digital Pro',
            'slug' => 'tensimeter-digital-pro',
            'description' => 'Alat tensi digital presisi.',
            'price' => 250000.00,
            'stock' => 20,
            'is_active' => true,
        ]);

        $img1 = ProductImage::create([
            'product_id' => $product->id,
            'path' => 'products/tensimeter-1.jpg',
            'thumb_path' => 'products/thumbs/tensimeter-1.jpg',
            'sort_order' => 1,
        ]);

        $img0 = ProductImage::create([
            'product_id' => $product->id,
            'path' => 'products/tensimeter-0.jpg',
            'thumb_path' => 'products/thumbs/tensimeter-0.jpg',
            'sort_order' => 0,
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'code' => 'MED-TNS-999',
        ]);

        $this->assertEquals($store->id, $product->store->id);
        $this->assertEquals($category->id, $product->category->id);
        $this->assertCount(1, $category->fresh()->products);
        $this->assertCount(1, $store->fresh()->products);

        // Verify images ordering and primaryImage (oldest of sort_order)
        $this->assertCount(2, $product->images);
        $this->assertEquals($img0->id, $product->primaryImage->id);

        // Verify formatted price accessor
        $this->assertEquals('Rp 250.000', $product->formatted_price);
    }

    public function test_product_scopes_active_and_case_insensitive_search(): void
    {
        $city = City::factory()->create();
        $vendor = User::factory()->vendor()->create(['city_id' => $city->id]);
        $store = Store::factory()->create(['owner_user_id' => $vendor->id, 'city_id' => $city->id]);
        $category = Category::factory()->create();

        $activeProduct = Product::create([
            'store_id' => $store->id,
            'category_id' => $category->id,
            'code' => 'MED-KRS-100',
            'name' => 'Kursi Roda Lipat Aluminium',
            'slug' => 'kursi-roda-lipat-aluminium',
            'description' => 'Kursi roda kuat dan ringan.',
            'price' => 1500000.00,
            'stock' => 10,
            'is_active' => true,
        ]);

        $inactiveProduct = Product::create([
            'store_id' => $store->id,
            'category_id' => $category->id,
            'code' => 'MED-OXI-200',
            'name' => 'Pulse Oximeter Mini',
            'slug' => 'pulse-oximeter-mini',
            'description' => 'Oximeter stok habis.',
            'price' => 100000.00,
            'stock' => 0,
            'is_active' => false,
        ]);

        // Active scope
        $activeResults = Product::active()->get();
        $this->assertTrue($activeResults->contains('id', $activeProduct->id));
        $this->assertFalse($activeResults->contains('id', $inactiveProduct->id));

        // Case-insensitive search by lowercase term
        $searchResults = Product::search('kursi roda')->get();
        $this->assertTrue($searchResults->contains('id', $activeProduct->id));

        // Search by uppercase code
        $codeResults = Product::search('MED-KRS-100')->get();
        $this->assertTrue($codeResults->contains('id', $activeProduct->id));

        // Search by partial lowercase code
        $partialResults = Product::search('krs-100')->get();
        $this->assertTrue($partialResults->contains('id', $activeProduct->id));
    }

    public function test_deleting_store_cascades_delete_to_products_and_images(): void
    {
        $city = City::factory()->create();
        $vendor = User::factory()->vendor()->create(['city_id' => $city->id]);
        $store = Store::factory()->create(['owner_user_id' => $vendor->id, 'city_id' => $city->id]);
        $category = Category::factory()->create();

        $product = Product::factory()->create([
            'store_id' => $store->id,
            'category_id' => $category->id,
        ]);

        $image = ProductImage::create([
            'product_id' => $product->id,
            'path' => 'products/sample.jpg',
            'sort_order' => 0,
        ]);

        $store->delete();

        $this->assertDatabaseMissing('stores', ['id' => $store->id]);
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        $this->assertDatabaseMissing('product_images', ['id' => $image->id]);
    }
}
