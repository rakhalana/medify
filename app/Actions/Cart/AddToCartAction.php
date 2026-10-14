<?php

namespace App\Actions\Cart;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use App\Services\Cart\CartPricingService;
use Illuminate\Validation\ValidationException;

class AddToCartAction
{
    public function __construct(
        protected CartPricingService $pricingService
    ) {
    }

    /**
     * Add a product to the user's cart.
     *
     * @throws ValidationException
     */
    public function execute(User $user, int $productId, int $quantity = 1): array
    {
        if ($quantity < 1) {
            throw ValidationException::withMessages([
                'quantity' => ['Jumlah produk minimal 1 unit.'],
            ]);
        }

        /** @var Product|null $product */
        $product = Product::with('store')->find($productId);

        if (! $product || ! $product->is_active) {
            throw ValidationException::withMessages([
                'product_id' => ['Produk tidak ditemukan atau sedang tidak aktif.'],
            ]);
        }

        if ($product->stock < 1) {
            throw ValidationException::withMessages([
                'product_id' => ["Maaf, stok produk '{$product->name}' sedang habis."],
            ]);
        }

        /** @var Cart $cart */
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        /** @var CartItem|null $existingItem */
        $existingItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->first();

        $currentQty = $existingItem ? $existingItem->quantity : 0;
        $targetQty = $currentQty + $quantity;

        if ($targetQty > $product->stock) {
            $availableToAdd = max(0, $product->stock - $currentQty);
            throw ValidationException::withMessages([
                'quantity' => [
                    $availableToAdd > 0
                        ? "Stok tidak mencukupi. Anda sudah memiliki {$currentQty} unit di keranjang, hanya dapat menambah {$availableToAdd} unit lagi."
                        : "Jumlah produk di keranjang Anda ({$currentQty} unit) sudah mencapai batas maksimal stok yang tersedia ({$product->stock} unit).",
                ],
            ]);
        }

        if ($existingItem) {
            $existingItem->update(['quantity' => $targetQty]);
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => $targetQty,
            ]);
        }

        return $this->pricingService->getCartSummary($cart->fresh(), $user);
    }
}
