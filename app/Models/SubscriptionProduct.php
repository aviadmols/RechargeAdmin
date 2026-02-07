<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionProduct extends Model
{
    protected $fillable = [
        'title',
        'description',
        'shopify_variant_id',
        'recharge_product_id',
        'image_url',
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
        ];
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
