<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscription_products', function (Blueprint $table) {
            $table->string('subtitle')->nullable()->after('title')->comment('Subtitle under product name');
            $table->string('badge_1')->nullable()->after('image_url');
            $table->string('badge_2')->nullable()->after('badge_1');
            $table->decimal('original_price', 10, 2)->nullable()->after('badge_2')->comment('Display price before discount (e.g. 10.00 for $10)');
            $table->unsignedTinyInteger('discount_percent')->nullable()->after('original_price')->comment('Discount % for display (e.g. 50 for 50% off)');
            $table->unsignedInteger('default_quantity')->default(1)->after('discount_percent');
        });
    }

    public function down(): void
    {
        Schema::table('subscription_products', function (Blueprint $table) {
            $table->dropColumn([
                'subtitle', 'badge_1', 'badge_2',
                'original_price', 'discount_percent', 'default_quantity',
            ]);
        });
    }
};
