<?php

namespace Tests\Feature\Catalog;

use App\Models\Category;
use App\Models\City;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogTest extends TestCase
{
    use RefreshDatabase;

    private Store $storeSurabaya;
    private Store $storeJakarta;
    private Category $catDiagnostik;
    private Category $catMobilitas;

    protected function setUp(): void
    {
        parent::setUp();

        $citySurabaya = City::factory()->create(['name' => 'Surabaya']);
        $cityJakarta = City::factory()->create(['name' => 'Jakarta']);

        $vendor1 = User::factory()->vendor()->create(['city_id' => $citySurabaya->id]);
        $vendor2 = User::factory()->vendor()->create(['city_id' => $cityJakarta->id]);

        $this->storeSurabaya = Store::factory()->create([
            'owner_user_id' => $vendor1->id,
            'city_id' => $citySurabaya->id,
            'name' => 'Toko Alkes Sehat',
            'slug' => 'toko-alkes-sehat',
        ]);

        $this->storeJakarta = Store::factory()->create([
            'owner_user_id' => $vendor2->id,
            'city_id' => $cityJakarta->id,
            'name' => 'Medika Jaya Mandiri',
            'slug' => 'medika-jaya-mandiri',
        ]);

        $this->catDiagnostik = Category::factory()->create([
            'name' => 'Diagnostik',
            'slug' => 'diagnostik',
        ]);

        $this->catMobilitas = Category::factory()->create([
            'name' => 'Mobilitas',
            'slug' => 'mobilitas',
        ]);
    }

    public function test_catalog_home_page_can_be_rendered_for_guests_and_users(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Medify');
        $response->assertSee('Katalog Resmi Multi-Vendor Medify');

        // Authenticated user
        $customer = User::factory()->create();
        $authResponse = $this->actingAs($customer)->get('/');
        $authResponse->assertStatus(200);
        $authResponse->assertSee($customer->name);
    }

    public function test_catalog_displays_active_products_and_hides_inactive_ones(): void
    {
        $activeProduct = Product::factory()->create([
            'store_id' => $this->storeSurabaya->id,
            'category_id' => $this->catDiagnostik->id,
            'name' => 'Tensimeter Digital DrPro',
            'is_active' => true,
        ]);

        $inactiveProduct = Product::factory()->create([
            'store_id' => $this->storeSurabaya->id,
            'category_id' => $this->catDiagnostik->id,
            'name' => 'Termometer Rusak',
            'is_active' => false,
        ]);

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Tensimeter Digital DrPro');
        $response->assertDontSee('Termometer Rusak');
    }

    public function test_catalog_filters_by_category_slug(): void
    {
        $prodDiagnostik = Product::factory()->create([
            'store_id' => $this->storeSurabaya->id,
            'category_id' => $this->catDiagnostik->id,
            'name' => 'Tensimeter Digital Khusus',
        ]);

        $prodMobilitas = Product::factory()->create([
            'store_id' => $this->storeSurabaya->id,
            'category_id' => $this->catMobilitas->id,
            'name' => 'Kursi Roda Lipat Nyaman',
        ]);

        $response = $this->get('/?category=diagnostik');
        $response->assertStatus(200);
        $response->assertSee('Tensimeter Digital Khusus');
        $response->assertDontSee('Kursi Roda Lipat Nyaman');
    }

    public function test_catalog_filters_by_store_slug(): void
    {
        $prodSby = Product::factory()->create([
            'store_id' => $this->storeSurabaya->id,
            'category_id' => $this->catDiagnostik->id,
            'name' => 'Barang Surabaya Saja',
        ]);

        $prodJkt = Product::factory()->create([
            'store_id' => $this->storeJakarta->id,
            'category_id' => $this->catDiagnostik->id,
            'name' => 'Barang Jakarta Saja',
        ]);

        $response = $this->get('/?store=toko-alkes-sehat');
        $response->assertStatus(200);
        $response->assertSee('Barang Surabaya Saja');
        $response->assertDontSee('Barang Jakarta Saja');
    }

    public function test_catalog_case_insensitive_search_query(): void
    {
        $product = Product::factory()->create([
            'store_id' => $this->storeSurabaya->id,
            'category_id' => $this->catDiagnostik->id,
            'name' => 'Pulse Oximeter Fingertip Medis',
            'code' => 'MED-OXI-777',
        ]);

        // Search by lowercase keyword
        $resp1 = $this->get('/?q=oximeter');
        $resp1->assertStatus(200);
        $resp1->assertSee('Pulse Oximeter Fingertip Medis');

        // Search by uppercase code
        $resp2 = $this->get('/?q=MED-OXI-777');
        $resp2->assertStatus(200);
        $resp2->assertSee('Pulse Oximeter Fingertip Medis');
    }

    public function test_catalog_sorts_by_price_and_latest(): void
    {
        $cheap = Product::factory()->create([
            'store_id' => $this->storeSurabaya->id,
            'category_id' => $this->catDiagnostik->id,
            'name' => 'Produk Murah',
            'price' => 50000,
        ]);

        $expensive = Product::factory()->create([
            'store_id' => $this->storeSurabaya->id,
            'category_id' => $this->catDiagnostik->id,
            'name' => 'Produk Mahal',
            'price' => 5000000,
        ]);

        $responseAsc = $this->get('/?sort=price_asc');
        $responseAsc->assertStatus(200);
        $content = $responseAsc->getContent();
        $this->assertIsString($content);
        $this->assertTrue(strpos($content, 'Produk Murah') < strpos($content, 'Produk Mahal'));

        $responseDesc = $this->get('/?sort=price_desc');
        $responseDesc->assertStatus(200);
        $contentDesc = $responseDesc->getContent();
        $this->assertIsString($contentDesc);
        $this->assertTrue(strpos($contentDesc, 'Produk Mahal') < strpos($contentDesc, 'Produk Murah'));
    }

    public function test_product_detail_page_renders_with_store_and_category_info(): void
    {
        $product = Product::factory()->create([
            'store_id' => $this->storeSurabaya->id,
            'category_id' => $this->catDiagnostik->id,
            'name' => 'Nebulizer Kompresor Deluxe',
            'slug' => 'nebulizer-kompresor-deluxe',
            'description' => 'Spesifikasi lengkap nebulizer.',
        ]);

        $response = $this->get('/products/' . $product->slug);
        $response->assertStatus(200);
        $response->assertSee('Nebulizer Kompresor Deluxe');
        $response->assertSee('Toko Alkes Sehat');
        $response->assertSee('Surabaya');
        $response->assertSee('Diagnostik');
        $response->assertSee('Spesifikasi lengkap nebulizer.');
    }

    public function test_product_detail_page_aborts_404_for_inactive_product(): void
    {
        $inactive = Product::factory()->create([
            'store_id' => $this->storeSurabaya->id,
            'category_id' => $this->catDiagnostik->id,
            'name' => 'Produk Nonaktif',
            'slug' => 'produk-nonaktif',
            'is_active' => false,
        ]);

        $response = $this->get('/products/' . $inactive->slug);
        $response->assertStatus(404);
    }

    public function test_quick_view_ajax_returns_json_payload(): void
    {
        $product = Product::factory()->create([
            'store_id' => $this->storeSurabaya->id,
            'category_id' => $this->catDiagnostik->id,
            'name' => 'Tensimeter Cepat',
            'price' => 200000,
        ]);

        $response = $this->getJson('/ajax/products/' . $product->id);
        $response->assertStatus(200);
        $response->assertJson([
            'id' => $product->id,
            'name' => 'Tensimeter Cepat',
            'price' => 200000,
            'formatted_price' => 'Rp 200.000',
            'store_name' => 'Toko Alkes Sehat',
            'store_city' => 'Surabaya',
        ]);
    }

    public function test_suggest_ajax_returns_matching_suggestions(): void
    {
        Product::factory()->create([
            'store_id' => $this->storeSurabaya->id,
            'category_id' => $this->catDiagnostik->id,
            'name' => 'Kursi Roda Standar',
        ]);

        $response = $this->getJson('/ajax/products/suggest?q=kursi');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'code', 'slug', 'price', 'formatted_price', 'store_name', 'store_city', 'url'],
            ],
        ]);
        $response->assertJsonFragment([
            'name' => 'Kursi Roda Standar',
        ]);
    }
}
