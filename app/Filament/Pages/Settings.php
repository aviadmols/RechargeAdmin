<?php

namespace App\Filament\Pages;

use App\Models\RechargeSettings;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class Settings extends Page
{
    protected static ?string $title = 'Recharge Settings';

    protected string $view = 'filament.pages.settings';

    public string $base_url = '';

    public string $api_version = '';

    public string $store_domain = '';

    public string $token = '';

    public bool $enable_cancel = true;

    public bool $enable_swap = true;

    public bool $enable_pause = true;

    public bool $enable_address_update = true;

    public ?int $cache_ttl_orders = 120;

    public ?int $cache_ttl_subscriptions = 60;

    public function mount(): void
    {
        $settings = RechargeSettings::first();
        if ($settings) {
            $this->base_url = $settings->base_url ?? 'https://api.rechargeapps.com';
            $this->api_version = $settings->api_version ?? '';
            $this->store_domain = $settings->store_domain ?? '';
            $this->enable_cancel = $settings->isFeatureEnabled('enable_cancel');
            $this->enable_swap = $settings->isFeatureEnabled('enable_swap');
            $this->enable_pause = $settings->isFeatureEnabled('enable_pause');
            $this->enable_address_update = $settings->isFeatureEnabled('enable_address_update');
            $this->cache_ttl_orders = $settings->cache_ttl_orders;
            $this->cache_ttl_subscriptions = $settings->cache_ttl_subscriptions;
        } else {
            $this->base_url = 'https://api.rechargeapps.com';
            $this->api_version = '2021-11';
        }
    }

    public function save(): void
    {
        $settings = RechargeSettings::first() ?? new RechargeSettings;
        $settings->base_url = $this->base_url ?: $settings->base_url;
        $settings->api_version = $this->api_version ?: null;
        $settings->store_domain = $this->store_domain ?: null;
        $settings->cache_ttl_orders = $this->cache_ttl_orders;
        $settings->cache_ttl_subscriptions = $this->cache_ttl_subscriptions;
        $settings->enabled_features = array_merge($settings->enabled_features ?? [], [
            'enable_cancel' => $this->enable_cancel,
            'enable_swap' => $this->enable_swap,
            'enable_pause' => $this->enable_pause,
            'enable_address_update' => $this->enable_address_update,
        ]);
        if ($this->token !== '') {
            $settings->token = $this->token;
        }
        $settings->save();
        $this->token = '';
        Notification::make()->title('Settings saved.')->success()->send();
    }

    public function testConnection(): void
    {
        try {
            $ok = app(\App\Services\RechargeService::class)->testConnection();
            if ($ok) {
                Notification::make()->title('Recharge API connection successful.')->success()->send();
            } else {
                Notification::make()->title('Recharge API connection failed.')->danger()->send();
            }
        } catch (\Throwable $e) {
            Notification::make()->title('Error: ' . $e->getMessage())->danger()->send();
        }
    }
}
