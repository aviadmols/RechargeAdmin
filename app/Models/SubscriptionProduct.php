<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionProduct extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'shopify_variant_id',
        'recharge_product_id',
        'image_url',
        'badge_1',
        'badge_2',
        'original_price',
        'discount_percent',
        'default_quantity',
        'first_order_price',
        'order_interval_frequency',
        'order_interval_unit',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'order_interval_frequency' => 'integer',
            'sort_order' => 'integer',
            'original_price' => 'decimal:2',
            'discount_percent' => 'integer',
            'default_quantity' => 'integer',
            'first_order_price' => 'decimal:2',
        ];
    }

    public function getDiscountedPriceAttribute(): ?float
    {
        if ($this->original_price === null) {
            return null;
        }
        $pct = $this->discount_percent ?? 0;
        return round((float) $this->original_price * (1 - $pct / 100), 2);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('title');
    }
}
