<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'brand_id',
        'name',
        'slug',
        'short_desc',
        'description',
        'base_price',
        'is_active',
        'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function primaryImage(): HasMany
    {
        return $this->hasMany(ProductImage::class)->where('is_primary', true);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'reviewable_id')->where('reviewable_type', Review::TYPE_PRODUCT);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function getThumbnailAttribute(): ?string
    {
        $primary = $this->images->firstWhere('is_primary', true) ?? $this->images->first();

        return $primary?->path;
    }

    /**
     * Total stock across all variants.
     */
    public function getTotalStockAttribute(): int
    {
        return (int) $this->variants->sum('stock_quantity');
    }

    /**
     * Lowest variant price, falling back to base_price when there are no variants.
     */
    public function getDisplayPriceAttribute(): string
    {
        $min = $this->variants->min('price');

        return $min !== null ? (string) $min : (string) $this->base_price;
    }

    public function getAverageRatingAttribute(): float
    {
        return round((float) $this->reviews()->where('is_approved', true)->avg('rating'), 1);
    }
}
