<?php

namespace Tests\Feature\Cart;

use App\Models\Cart;
use App\Models\Category;
use App\Models\City;
use App\Models\Product;
use App\Models\Store;
use App\Models\TaxRate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;
    private User $otherCustomer;
    private Store $storeSurabaya;
    private Store $storeJakarta;
    private Product $productTensimeter;
    private Product $productKursiRoda;

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
            'name' => 'Toko Alkes Sehat Surabaya',
            'slug' => 'toko-alkes-sehat-surabaya',
        ]);

        $this->storeJakarta = Store::factory()->create([
            'owner_user_id' => $vendor2->id,
            'city_id' => $cityJakarta->id,
            'name' => 'Medika Jaya Jakarta',
            'slug' => 'medika-jaya-jakarta',
        ]);

        $category = Category::factory()->create(['name' => 'Diagnostik']);

        // Default active tax rate 11%
        TaxRate::create([
            'name' => 'PPN Standar 11%',
            'rate_percent' => 11.00,
            'effective_from' => now()->toDateString(),
            'is_active' => true,
        ]);

        $this->productTensimeter = Product::factory()->create([
            'store_id' => $this->storeSurabaya->id,
            'category_id' => $category->id,
            'code' => 'TNS-01-001',
            'name' => 'Tensimeter Digital Omron',
            'price' => 500000,
            'stock' => 10,
            'is_active' => true,
        ]);

        $this->productKursiRoda = Product::factory()->create([
            'store_id' => $this->storeJakarta->id,
            'category_id' => $category->id,
            'code' => 'ROD-02-001',
            'name' => 'Kursi Roda Standard',
            'price' => 1200000,
            'stock' => 5,
            'is_active' => true,
        ]);

        $this->customer = User::factory()->create([
            'city_id' => $citySurabaya->id,
        ]);

        $this->otherCustomer = User::factory()->create([
            'city_id' => $cityJakarta->id,
        ]);
    }

    public function test_guest_is_redirected_to_login_when_accessing_cart(): void
    {
        $response = $this->get('/cart');
        $response->assertRedirect('/login');
    }

    public function test_guest_gets_unauthenticated_json_response_on_ajax_cart(): void
    {
        $response = $this->getJson('/ajax/cart');
        $response->assertStatus(401);
        $response->assertJson(['code' => 'UNAUTHENTICATED']);
    }

    public function test_customer_can_view_cart_page(): void
    {
        $response = $this->actingAs($this->customer)->get('/cart');
        $response->assertStatus(200);
        $response->assertSee('Keranjang Belanja');
    }

    public function test_customer_can_add_product_to_cart_via_ajax(): void
    {
        $response = $this->actingAs($this->customer)->postJson('/ajax/cart/items', [
            'product_id' => $this->productTensimeter->id,
            'quantity' => 2,
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.total_items_count', 2);
        $response->assertJsonPath('data.pricing.subtotal', 1000000);

        $this->assertDatabaseHas('cart_items', [
            'product_id' => $this->productTensimeter->id,
            'quantity' => 2,
        ]);
    }

    public function test_customer_cannot_add_inactive_or_out_of_stock_product(): void
    {
        $inactiveProduct = Product::factory()->create([
            'store_id' => $this->storeSurabaya->id,
            'is_active' => false,
            'stock' => 5,
        ]);

        $outOfStockProduct = Product::factory()->create([
            'store_id' => $this->storeSurabaya->id,
            'is_active' => true,
            'stock' => 0,
        ]);

        $res1 = $this->actingAs($this->customer)->postJson('/ajax/cart/items', [
            'product_id' => $inactiveProduct->id,
            'quantity' => 1,
        ]);
        $res1->assertStatus(422);

        $res2 = $this->actingAs($this->customer)->postJson('/ajax/cart/items', [
            'product_id' => $outOfStockProduct->id,
            'quantity' => 1,
        ]);
        $res2->assertStatus(422);
    }

    public function test_customer_cannot_add_more_than_available_stock(): void
    {
        $response = $this->actingAs($this->customer)->postJson('/ajax/cart/items', [
            'product_id' => $this->productKursiRoda->id,
            'quantity' => 10, // Available stock is only 5
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['quantity']]);
    }

    public function test_cart_groups_items_by_multiple_stores_accurately(): void
    {
        // Add item from Store Surabaya
        $this->actingAs($this->customer)->postJson('/ajax/cart/items', [
            'product_id' => $this->productTensimeter->id,
            'quantity' => 1,
        ]);

        // Add item from Store Jakarta
        $response = $this->actingAs($this->customer)->postJson('/ajax/cart/items', [
            'product_id' => $this->productKursiRoda->id,
            'quantity' => 2,
        ]);

        $response->assertStatus(200);
        $data = $response->json('data');

        // Total 2 distinct stores in cart
        $this->assertCount(2, $data['stores']);
        $this->assertEquals(3, $data['total_items_count']);

        // Check stores array content
        $storeNames = collect($data['stores'])->pluck('store_name')->all();
        $this->assertContains('Toko Alkes Sehat Surabaya', $storeNames);
        $this->assertContains('Medika Jaya Jakarta', $storeNames);
    }

    public function test_customer_can_update_cart_item_quantity(): void
    {
        $this->actingAs($this->customer)->postJson('/ajax/cart/items', [
            'product_id' => $this->productTensimeter->id,
            'quantity' => 1,
        ]);

        $cart = Cart::where('user_id', $this->customer->id)->firstOrFail();
        $cartItem = $cart->items()->firstOrFail();

        $response = $this->actingAs($this->customer)->patchJson("/ajax/cart/items/{$cartItem->id}", [
            'quantity' => 4,
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.total_items_count', 4);
        $this->assertEquals(4, $cartItem->fresh()->quantity);
    }

    public function test_customer_cannot_update_cart_item_to_exceed_stock(): void
    {
        $this->actingAs($this->customer)->postJson('/ajax/cart/items', [
            'product_id' => $this->productKursiRoda->id, // stock = 5
            'quantity' => 1,
        ]);

        $cart = Cart::where('user_id', $this->customer->id)->firstOrFail();
        $cartItem = $cart->items()->firstOrFail();

        $response = $this->actingAs($this->customer)->patchJson("/ajax/cart/items/{$cartItem->id}", [
            'quantity' => 99,
        ]);

        $response->assertStatus(422);
    }

    public function test_customer_can_remove_item_from_cart(): void
    {
        $this->actingAs($this->customer)->postJson('/ajax/cart/items', [
            'product_id' => $this->productTensimeter->id,
            'quantity' => 2,
        ]);

        $cart = Cart::where('user_id', $this->customer->id)->firstOrFail();
        $cartItem = $cart->items()->firstOrFail();

        $response = $this->actingAs($this->customer)->deleteJson("/ajax/cart/items/{$cartItem->id}");
        $response->assertStatus(200);
        $response->assertJsonPath('data.is_empty', true);

        $this->assertDatabaseMissing('cart_items', ['id' => $cartItem->id]);
    }

    public function test_customer_can_clear_entire_cart(): void
    {
        $this->actingAs($this->customer)->postJson('/ajax/cart/items', [
            'product_id' => $this->productTensimeter->id,
            'quantity' => 1,
        ]);
        $this->actingAs($this->customer)->postJson('/ajax/cart/items', [
            'product_id' => $this->productKursiRoda->id,
            'quantity' => 1,
        ]);

        $response = $this->actingAs($this->customer)->deleteJson('/ajax/cart');
        $response->assertStatus(200);
        $response->assertJsonPath('data.is_empty', true);
        $response->assertJsonPath('data.total_items_count', 0);

        $cart = Cart::where('user_id', $this->customer->id)->firstOrFail();
        $this->assertCount(0, $cart->items);
    }

    public function test_user_cannot_update_or_delete_another_users_cart_item(): void
    {
        // Customer A adds item to their cart
        $this->actingAs($this->customer)->postJson('/ajax/cart/items', [
            'product_id' => $this->productTensimeter->id,
            'quantity' => 1,
        ]);

        $cart = Cart::where('user_id', $this->customer->id)->firstOrFail();
        $cartItem = $cart->items()->firstOrFail();

        // Customer B attempts to update Customer A's cart item
        $responseUpdate = $this->actingAs($this->otherCustomer)->patchJson("/ajax/cart/items/{$cartItem->id}", [
            'quantity' => 3,
        ]);
        $responseUpdate->assertStatus(403);

        // Customer B attempts to delete Customer A's cart item
        $responseDelete = $this->actingAs($this->otherCustomer)->deleteJson("/ajax/cart/items/{$cartItem->id}");
        $responseDelete->assertStatus(403);
    }

    public function test_pricing_calculation_accurately_applies_active_ppn_and_free_shipping(): void
    {
        // Tensimeter 500.000 x 2 = 1.000.000
        $this->actingAs($this->customer)->postJson('/ajax/cart/items', [
            'product_id' => $this->productTensimeter->id,
            'quantity' => 2,
        ]);

        $response = $this->actingAs($this->customer)->getJson('/ajax/cart');
        $response->assertStatus(200);

        $pricing = $response->json('data.pricing');

        // Subtotal: 1.000.000
        $this->assertEquals(1000000, $pricing['subtotal']);
        // PPN 11%: 110.000
        $this->assertEquals(11, $pricing['tax_rate_percent']);
        $this->assertEquals(110000, $pricing['tax_amount']);
        // Ongkir Rp0: 0
        $this->assertEquals(0, $pricing['shipping_fee']);
        // Grand Total: 1.110.000
        $this->assertEquals(1110000, $pricing['grand_total']);
    }
}
