<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_products', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('shopify_variant_id')->comment('Shopify variant ID – used as external_variant_id in Recharge');
            $table->string('recharge_product_id')->nullable()->comment('Recharge product ID for reference');
            $table->string('image_url')->nullable();
            $table->unsignedSmallInteger('order_interval_frequency')->default(1);
            $table->string('order_interval_unit', 20)->default('month');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_products');
    }
};
