<?php

namespace App\Services\Cart;

use App\Models\Cart;
use App\Models\TaxRate;
use App\Models\User;

class CartPricingService
{
    /**
     * Get the active VAT/PPN tax rate percentage.
     */
    public function getActiveTaxRate(): float
    {
        /** @var TaxRate|null $activeTax */
        $activeTax = TaxRate::where('is_active', true)->first();

        return $activeTax ? (float) $activeTax->rate_percent : 11.0;
    }

    /**
     * Generate complete authoritative cart summary with store groupings and calculations.
     *
     * @return array<string, mixed>
     */
    public function getCartSummary(Cart $cart, ?User $user = null): array
    {
        // Eager load items with product, store, city, and images
        $cart->loadMissing([
            'items.product.store.city',
            'items.product.images',
        ]);

        $taxRate = $this->getActiveTaxRate();
        $subtotal = 0.0;
        $totalItemsCount = 0;
        $storesGrouped = [];

        foreach ($cart->items as $item) {
            $product = $item->product;
            if (! $product) {
                continue;
            }

            $store = $product->store;
            $storeId = $store ? $store->id : 0;
            $lineSubtotal = (float) ($item->quantity * $product->price);
            $subtotal += $lineSubtotal;
            $totalItemsCount += $item->quantity;

            if (! isset($storesGrouped[$storeId])) {
                $storesGrouped[$storeId] = [
                    'store_id' => $storeId,
                    'store_name' => $store?->name ?? 'Toko Tidak Diketahui',
                    'store_slug' => $store?->slug ?? '',
                    'store_city' => $store?->city?->name ?? 'Indonesia',
                    'store_city_id' => $store?->city_id ?? null,
                    'items' => [],
                    'store_subtotal' => 0.0,
                    'formatted_store_subtotal' => 'Rp 0',
                ];
            }

            $primaryImg = $product->primaryImage?->path
                ? asset($product->primaryImage->path)
                : ($product->images->first()?->path ? asset($product->images->first()->path) : null);

            $storesGrouped[$storeId]['items'][] = [
                'id' => $item->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_code' => $product->code,
                'product_slug' => $product->slug,
                'price' => (float) $product->price,
                'formatted_price' => 'Rp ' . number_format((float) $product->price, 0, ',', '.'),
                'quantity' => $item->quantity,
                'subtotal' => $lineSubtotal,
                'formatted_subtotal' => 'Rp ' . number_format($lineSubtotal, 0, ',', '.'),
                'stock' => $product->stock,
                'is_active' => $product->is_active,
                'is_stock_available' => $product->is_active && $product->stock >= $item->quantity,
                'image_url' => $primaryImg,
            ];

            $storesGrouped[$storeId]['store_subtotal'] += $lineSubtotal;
            $storesGrouped[$storeId]['formatted_store_subtotal'] = 'Rp ' . number_format($storesGrouped[$storeId]['store_subtotal'], 0, ',', '.');
        }

        // Calculations per PRD (FR-TAX-03, FR-CART-05, FR-CART-07)
        $taxAmount = (float) round($subtotal * ($taxRate / 100));
        $shippingFee = 0.0; // Ongkir Rp0 (Gratis Ongkir)
        $grandTotal = $subtotal + $taxAmount + $shippingFee;

        // Postpaid COD eligibility check (FR-PAY-02)
        $isEligibleForPostpaid = $this->isEligibleForPostpaid($user, array_values($storesGrouped));

        return [
            'cart_id' => $cart->id,
            'total_items_count' => $totalItemsCount,
            'is_empty' => $totalItemsCount === 0,
            'stores' => array_values($storesGrouped),
            'pricing' => [
                'subtotal' => $subtotal,
                'formatted_subtotal' => 'Rp ' . number_format($subtotal, 0, ',', '.'),
                'tax_rate_percent' => $taxRate,
                'tax_amount' => $taxAmount,
                'formatted_tax_amount' => 'Rp ' . number_format($taxAmount, 0, ',', '.'),
                'shipping_fee' => $shippingFee,
                'formatted_shipping_fee' => 'Rp 0',
                'grand_total' => $grandTotal,
                'formatted_grand_total' => 'Rp ' . number_format($grandTotal, 0, ',', '.'),
            ],
            'shipping_badge' => 'Ongkir Rp0 (Gratis Ongkir)',
            'postpaid_eligibility' => [
                'eligible' => $isEligibleForPostpaid,
                'reason' => $isEligibleForPostpaid
                    ? 'Seluruh toko berada dalam kota yang sama dengan alamat Anda.'
                    : 'Metode COD/Postpaid hanya tersedia jika seluruh toko berada di kota yang sama dengan Anda.',
            ],
        ];
    }

    /**
     * Check if cart is eligible for Postpaid (COD) sekota rule (FR-PAY-02).
     *
     * @param array<int, array<string, mixed>> $stores
     */
    public function isEligibleForPostpaid(?User $user, array $stores): bool
    {
        if (! $user || ! $user->city_id || empty($stores)) {
            return false;
        }

        foreach ($stores as $storeGroup) {
            if ($storeGroup['store_city_id'] !== $user->city_id) {
                return false;
            }
        }

        return true;
    }
}
