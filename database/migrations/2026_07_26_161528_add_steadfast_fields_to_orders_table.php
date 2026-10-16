<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('consignment_id')->nullable()->after('payment_method');
            $table->string('tracking_code')->nullable()->after('consignment_id');
            $table->string('courier_status')->nullable()->after('tracking_code');
            $table->timestamp('courier_pushed_at')->nullable()->after('courier_status');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'consignment_id', 'tracking_code', 'courier_status', 'courier_pushed_at',
            ]);
        });
    }
};
