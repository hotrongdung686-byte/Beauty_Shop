<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    public const TYPE_PRODUCT = 'product';

    public const TYPE_SERVICE = 'service';

    protected $fillable = [
        'user_id',
        'reviewable_type',
        'reviewable_id',
        'rating',
        'comment',
        'is_approved',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'is_approved' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The reviewable record (Product or Service), resolved manually since
     * reviewable_type stores a short enum ('product'|'service') rather than
     * a fully-qualified class name.
     */
    public function getReviewableAttribute(): Product|Service|null
    {
        return match ($this->reviewable_type) {
            self::TYPE_PRODUCT => Product::find($this->reviewable_id),
            self::TYPE_SERVICE => Service::find($this->reviewable_id),
            default => null,
        };
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopeForProduct($query, int $productId)
    {
        return $query->where('reviewable_type', self::TYPE_PRODUCT)->where('reviewable_id', $productId);
    }

    public function scopeForService($query, int $serviceId)
    {
        return $query->where('reviewable_type', self::TYPE_SERVICE)->where('reviewable_id', $serviceId);
    }
}
