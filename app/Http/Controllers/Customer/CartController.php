<?php

namespace App\Http\Controllers\Customer;

use App\Actions\Cart\AddToCartAction;
use App\Actions\Cart\ClearCartAction;
use App\Actions\Cart\RemoveCartItemAction;
use App\Actions\Cart\UpdateCartItemAction;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Services\Cart\CartPricingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(
        protected CartPricingService $pricingService
    ) {
    }

    /**
     * Display the shopping cart page.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        if (! $user) {
            return redirect()->guest(route('login'))
                ->with('warning', 'Silakan masuk ke akun Anda terlebih dahulu untuk melihat keranjang belanja.');
        }

        /** @var Cart $cart */
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);
        $cartSummary = $this->pricingService->getCartSummary($cart, $user);

        return view('cart.index', compact('cartSummary'));
    }

    /**
     * Get the current cart summary via AJAX.
     */
    public function getCart(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json([
                'message' => 'Silakan masuk ke akun Anda terlebih dahulu.',
                'code' => 'UNAUTHENTICATED',
            ], 401);
        }

        /** @var Cart $cart */
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);
        $summary = $this->pricingService->getCartSummary($cart, $user);

        return response()->json([
            'data' => $summary,
            'meta' => [
                'request_id' => $request->header('X-Request-Id', (string) str()->uuid()),
            ],
        ]);
    }

    /**
     * Add a product to the cart via AJAX.
     */
    public function addItem(Request $request, AddToCartAction $action): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json([
                'message' => 'Silakan masuk ke akun Anda terlebih dahulu untuk menambahkan produk.',
                'code' => 'UNAUTHENTICATED',
                'redirect_url' => route('login'),
            ], 401);
        }

        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        try {
            $summary = $action->execute(
                $user,
                (int) $validated['product_id'],
                (int) ($validated['quantity'] ?? 1)
            );

            return response()->json([
                'message' => 'Produk berhasil ditambahkan ke keranjang belanja!',
                'data' => $summary,
                'meta' => [
                    'request_id' => $request->header('X-Request-Id', (string) str()->uuid()),
                ],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => $e->validator->errors()->first(),
                'errors' => $e->validator->errors(),
                'code' => 'VALIDATION_ERROR',
            ], 422);
        }
    }

    /**
     * Update the quantity of a cart item via AJAX.
     */
    public function updateItem(Request $request, int $cartItemId, UpdateCartItemAction $action): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        try {
            $summary = $action->execute($user, $cartItemId, (int) $validated['quantity']);

            return response()->json([
                'message' => 'Jumlah produk berhasil diperbarui.',
                'data' => $summary,
                'meta' => [
                    'request_id' => $request->header('X-Request-Id', (string) str()->uuid()),
                ],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => $e->validator->errors()->first(),
                'errors' => $e->validator->errors(),
                'code' => 'VALIDATION_ERROR',
            ], 422);
        }
    }

    /**
     * Remove an item from the cart via AJAX.
     */
    public function removeItem(Request $request, int $cartItemId, RemoveCartItemAction $action): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $summary = $action->execute($user, $cartItemId);

        return response()->json([
            'message' => 'Produk berhasil dihapus dari keranjang belanja.',
            'data' => $summary,
            'meta' => [
                'request_id' => $request->header('X-Request-Id', (string) str()->uuid()),
            ],
        ]);
    }

    /**
     * Empty the entire shopping cart via AJAX.
     */
    public function clearCart(Request $request, ClearCartAction $action): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $summary = $action->execute($user);

        return response()->json([
            'message' => 'Seluruh produk di keranjang berhasil dikosongkan.',
            'data' => $summary,
            'meta' => [
                'request_id' => $request->header('X-Request-Id', (string) str()->uuid()),
            ],
        ]);
    }
}
