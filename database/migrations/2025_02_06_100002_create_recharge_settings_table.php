<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recharge_settings', function (Blueprint $table) {
            $table->id();
            $table->text('token_encrypted')->nullable();
            $table->string('base_url')->default('https://api.rechargeapps.com');
            $table->string('api_version', 20)->nullable();
            $table->string('store_domain')->nullable();
            $table->json('enabled_features')->nullable();
            $table->json('brand')->nullable();
            $table->unsignedInteger('cache_ttl_orders')->nullable();
            $table->unsignedInteger('cache_ttl_subscriptions')->nullable();
            $table->timestamp('last_api_success_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recharge_settings');
    }
};
