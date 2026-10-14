<?php

namespace App\Actions\Cart;

use App\Models\CartItem;
use App\Models\User;
use App\Services\Cart\CartPricingService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;

class UpdateCartItemAction
{
    public function __construct(
        protected CartPricingService $pricingService
    ) {
    }

    /**
     * Update the quantity of a specific cart item.
     *
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function execute(User $user, int $cartItemId, int $quantity): array
    {
        /** @var CartItem|null $cartItem */
        $cartItem = CartItem::with(['cart', 'product'])->find($cartItemId);

        if (! $cartItem) {
            throw ValidationException::withMessages([
                'cart_item' => ['Item keranjang tidak ditemukan.'],
            ]);
        }

        // Authorization / IDOR defense: ensure cart item belongs to the authenticated user
        if ($cartItem->cart->user_id !== $user->id) {
            throw new AuthorizationException('Anda tidak memiliki akses ke item keranjang ini.');
        }

        if ($quantity < 1) {
            throw ValidationException::withMessages([
                'quantity' => ['Jumlah produk minimal 1 unit. Gunakan tombol hapus untuk mengeluarkan produk dari keranjang.'],
            ]);
        }

        $product = $cartItem->product;
        if (! $product || ! $product->is_active) {
            throw ValidationException::withMessages([
                'cart_item' => ['Produk ini sedang tidak tersedia atau tidak aktif.'],
            ]);
        }

        if ($quantity > $product->stock) {
            throw ValidationException::withMessages([
                'quantity' => ["Jumlah melebihi stok yang tersedia ({$product->stock} unit)."],
            ]);
        }

        $cartItem->update(['quantity' => $quantity]);

        return $this->pricingService->getCartSummary($cartItem->cart->fresh(), $user);
    }
}
