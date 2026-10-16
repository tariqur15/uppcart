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
        Schema::table('product_views', function (Blueprint $table) {
            $table->string('session_id', 64)->nullable()->index();
            $table->string('browser')->nullable();
            $table->string('os')->nullable();
            $table->string('device_type')->nullable()->comment('desktop, mobile, tablet');
            $table->string('country', 2)->nullable()->index();
            $table->string('city')->nullable();
            $table->string('traffic_source')->nullable()->comment('google, facebook, direct, referral, etc.');
            $table->string('referrer_url')->nullable();
            $table->string('landing_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_views', function (Blueprint $table) {
            $table->dropColumn([
                'session_id', 'browser', 'os', 'device_type',
                'country', 'city', 'traffic_source', 'referrer_url', 'landing_url',
            ]);
        });
    }
};
