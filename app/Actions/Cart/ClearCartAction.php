<?php

namespace App\Actions\Cart;

use App\Models\Cart;
use App\Models\User;
use App\Services\Cart\CartPricingService;

class ClearCartAction
{
    public function __construct(
        protected CartPricingService $pricingService
    ) {
    }

    /**
     * Clear all items in the user's cart.
     */
    public function execute(User $user): array
    {
        /** @var Cart|null $cart */
        $cart = Cart::where('user_id', $user->id)->first();

        if ($cart) {
            $cart->items()->delete();
        } else {
            $cart = Cart::create(['user_id' => $user->id]);
        }

        return $this->pricingService->getCartSummary($cart->fresh(), $user);
    }
}
