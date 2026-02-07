<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscription_products', function (Blueprint $table) {
            $table->decimal('first_order_price', 10, 2)->nullable()->after('default_quantity')
                ->comment('Price for first order (OTP) – sent to Recharge when creating subscription. Leave empty to use store/variant default.');
        });
    }

    public function down(): void
    {
        Schema::table('subscription_products', function (Blueprint $table) {
            $table->dropColumn('first_order_price');
        });
    }
};
