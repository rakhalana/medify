<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
    ];

    /**
     * The items contained within this cart.
     */
    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class, 'cart_id');
    }

    /**
     * The customer who owns this cart.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Total quantity count of all items.
     */
    public function getTotalCountAttribute(): int
    {
        return (int) $this->items()->sum('quantity');
    }
}
