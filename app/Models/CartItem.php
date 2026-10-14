<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
        ];
    }

    /**
     * Parent cart instance.
     */
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class, 'cart_id');
    }

    /**
     * Product associated with this cart item.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Calculate line item subtotal.
     */
    public function getSubtotalAttribute(): float
    {
        return (float) ($this->quantity * ($this->product?->price ?? 0));
    }

    /**
     * Formatted subtotal in IDR.
     */
    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }

    /**
     * Verify if requested quantity is still in stock.
     */
    public function isStockAvailable(): bool
    {
        if (! $this->product || ! $this->product->is_active) {
            return false;
        }

        return $this->product->stock >= $this->quantity;
    }
}
