<?php

namespace App\Models;

use App\Enums\StoreStatus;
use Database\Factories\StoreFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $owner_user_id
 * @property int $city_id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property StoreStatus $status
 * @property string|null $logo_path
 * @property string|null $signature_path
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read User $owner
 * @property-read City $city
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Product> $products
 */
class Store extends Model
{
    /** @use HasFactory<StoreFactory> */
    use HasFactory;

    protected $fillable = [
        'owner_user_id',
        'city_id',
        'name',
        'slug',
        'description',
        'status',
        'logo_path',
        'signature_path',
    ];

    protected function casts(): array
    {
        return [
            'status' => StoreStatus::class,
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'store_id');
    }

    public function isActive(): bool
    {
        return $this->status === StoreStatus::ACTIVE;
    }

    /**
     * Scope a query to only include active stores.
     *
     * @param Builder<self> $query
     * @return Builder<self>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', StoreStatus::ACTIVE->value);
    }
}
