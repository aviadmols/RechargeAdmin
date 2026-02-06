<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class RechargeSettings extends Model
{
    protected $table = 'recharge_settings';

    protected $fillable = [
        'base_url',
        'api_version',
        'store_domain',
        'enabled_features',
        'brand',
        'cache_ttl_orders',
        'cache_ttl_subscriptions',
        'last_api_success_at',
    ];

    protected function casts(): array
    {
        return [
            'enabled_features' => 'array',
            'brand' => 'array',
            'last_api_success_at' => 'datetime',
        ];
    }

    public function getDecryptedToken(): ?string
    {
        if (empty($this->token_encrypted)) {
            return null;
        }
        try {
            return Crypt::decryptString($this->token_encrypted);
        } catch (\Throwable) {
            return null;
        }
    }

    public function setTokenAttribute(?string $value): void
    {
        $this->attributes['token_encrypted'] = $value ? Crypt::encryptString($value) : null;
    }

    public static function singleton(): self
    {
        $row = self::first();
        if ($row) {
            return $row;
        }
        return self::create([
            'base_url' => 'https://api.rechargeapps.com',
            'enabled_features' => [
                'enable_cancel' => true,
                'enable_swap' => true,
                'enable_pause' => true,
                'enable_address_update' => true,
            ],
        ]);
    }

    public function isFeatureEnabled(string $key): bool
    {
        $features = $this->enabled_features ?? [];
        return (bool) ($features[$key] ?? true);
    }
}
