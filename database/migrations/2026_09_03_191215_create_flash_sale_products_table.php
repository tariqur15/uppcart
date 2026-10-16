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
        Schema::create('flash_sale_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flash_sale_id')->constrained('flash_sales')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('scope')->default('whole')->index();
            $table->string('variant')->nullable();
            $table->string('discount_type')->default('percentage');
            $table->decimal('discount_value', 10, 2)->nullable();
            $table->decimal('regular_price', 10, 2);
            $table->decimal('flash_price', 10, 2);
            $table->unsignedInteger('sale_stock')->nullable();
            $table->unsignedInteger('sold')->default(0);
            $table->unsignedInteger('max_per_customer')->nullable();
            $table->timestamps();

            $table->unique(['flash_sale_id', 'product_id', 'variant']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flash_sale_products');
    }
};
