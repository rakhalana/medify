<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property int $store_id
 * @property int $category_id
 * @property string $code
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property string $price
 * @property int $stock
 * @property bool $is_active
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read string $formatted_price
 * @property-read Store $store
 * @property-read Category $category
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ProductImage> $images
 * @property-read ProductImage|null $primaryImage
 */
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    protected $fillable = [
        'store_id',
        'category_id',
        'code',
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class, 'product_id')->orderBy('sort_order', 'asc');
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProductImage::class, 'product_id')->oldestOfMany('sort_order');
    }

    /**
     * Format price to Indonesian Rupiah standard (Rp X.XXX.XXX)
     */
    protected function formattedPrice(): Attribute
    {
        return Attribute::make(
            get: fn () => 'Rp ' . number_format((float) $this->price, 0, ',', '.')
        );
    }

    /**
     * Scope query to only active products.
     *
     * @param Builder<self> $query
     * @return Builder<self>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope query for case-insensitive keyword search by name or product code.
     * Uses LOWER(...) LIKE pattern compatible with both PostgreSQL (using functional index) and SQLite.
     *
     * @param Builder<self> $query
     * @param string|null $term
     * @return Builder<self>
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        $termLower = '%' . mb_strtolower(trim($term)) . '%';

        return $query->where(function (Builder $subQuery) use ($termLower) {
            $subQuery->whereRaw('LOWER(name) LIKE ?', [$termLower])
                ->orWhereRaw('LOWER(code) LIKE ?', [$termLower]);
        });
    }
}
