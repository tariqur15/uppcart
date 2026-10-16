<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attributes', function (Blueprint $table) {
            $table->boolean('enable_archives')->default(false)->after('status');
            $table->string('default_sort_order', 50)->default('custom_ordering')->after('enable_archives');
        });
    }

    public function down(): void
    {
        Schema::table('attributes', function (Blueprint $table) {
            $table->dropColumn(['enable_archives', 'default_sort_order']);
        });
    }
};
