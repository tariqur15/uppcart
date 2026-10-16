<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('variants_enabled')->default(false)->after('status');
            $table->json('variant_options')->nullable()->after('variants_enabled');
            $table->json('variant_data')->nullable()->after('variant_options');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['variants_enabled', 'variant_options', 'variant_data']);
        });
    }
};
