<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('original_price', 10, 2)->nullable()->after('quantity');
            $table->decimal('tier_price', 10, 2)->nullable()->after('original_price');
            $table->decimal('discount', 10, 2)->default(0)->after('tier_price');
            $table->foreignId('product_tier_price_id')->nullable()->after('discount')->constrained('product_tier_prices')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['product_tier_price_id']);
            $table->dropColumn(['original_price', 'tier_price', 'discount', 'product_tier_price_id']);
        });
    }
};
