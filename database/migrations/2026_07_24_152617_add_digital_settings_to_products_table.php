<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->json('digital_files')->nullable()->after('type');
            $table->integer('download_limit')->nullable()->after('digital_files');
            $table->integer('download_expiry')->nullable()->after('download_limit');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['digital_files', 'download_limit', 'download_expiry']);
        });
    }
};
