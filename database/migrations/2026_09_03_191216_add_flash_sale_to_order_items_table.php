<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('flash_sale_id')->nullable()->after('product_tier_price_id')->constrained('flash_sales')->nullOnDelete();
            $table->foreignId('flash_sale_product_id')->nullable()->after('flash_sale_id')->constrained('flash_sale_products')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('flash_sale_product_id');
            $table->dropConstrainedForeignId('flash_sale_id');
        });
    }
};
