<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'badge_text',
        'image',
        'product_id',
        'custom_url',
        'button_text',
        'background_color',
        'sort_order',
        'is_active',
        'starts_at',
        'ends_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Only banners currently within their optional publish window.
     */
    public function scopeLive($query)
    {
        $now = now();

        return $query->active()
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now));
    }

    public function getImageUrlAttribute(): ?string
    {
        if ($this->image) {
            return asset('storage/'.$this->image);
        }

        if ($this->product?->thumbnail) {
            return asset('storage/'.$this->product->thumbnail);
        }

        return null;
    }

    public function getTargetUrlAttribute(): string
    {
        if ($this->product) {
            return route('products.show', $this->product);
        }

        return $this->custom_url ?: route('products.index');
    }

    public function getDisplayPriceAttribute(): ?string
    {
        return $this->product?->display_price;
    }
}
