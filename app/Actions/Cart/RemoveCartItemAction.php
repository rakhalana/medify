<?php

namespace App\Actions\Cart;

use App\Models\CartItem;
use App\Models\User;
use App\Services\Cart\CartPricingService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;

class RemoveCartItemAction
{
    public function __construct(
        protected CartPricingService $pricingService
    ) {
    }

    /**
     * Remove an item from the user's cart.
     *
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function execute(User $user, int $cartItemId): array
    {
        /** @var CartItem|null $cartItem */
        $cartItem = CartItem::with('cart')->find($cartItemId);

        if (! $cartItem) {
            throw ValidationException::withMessages([
                'cart_item' => ['Item keranjang tidak ditemukan.'],
            ]);
        }

        // Authorization / IDOR defense
        if ($cartItem->cart->user_id !== $user->id) {
            throw new AuthorizationException('Anda tidak memiliki akses ke item keranjang ini.');
        }

        $cart = $cartItem->cart;
        $cartItem->delete();

        return $this->pricingService->getCartSummary($cart->fresh(), $user);
    }
}
