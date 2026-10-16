<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('collections', function (Blueprint $table) {
            $table->string('collection_type')->default('manual')->after('image');
            $table->string('status')->default('active')->after('collection_type');
            $table->string('match_type')->nullable()->after('status');
            $table->integer('sort_order')->default(0)->after('match_type');
        });

        Schema::create('collection_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collection_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('collection_conditions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collection_id')->constrained()->cascadeOnDelete();
            $table->string('condition_field');
            $table->string('condition_operator');
            $table->string('condition_value');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collection_conditions');
        Schema::dropIfExists('collection_products');

        Schema::table('collections', function (Blueprint $table) {
            $table->dropColumn(['collection_type', 'status', 'match_type', 'sort_order']);
        });
    }
};
