<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('address_type')->nullable()->after('address');
            $table->string('division')->nullable()->after('address_type');
            $table->string('district')->nullable()->after('division');
            $table->string('thana')->nullable()->after('district');
            $table->string('landmark')->nullable()->after('thana');
            $table->string('delivery_option')->default('inside_dhaka')->after('landmark');
            $table->decimal('delivery_charge', 8, 2)->default(0)->after('delivery_option');
            $table->string('payment_method')->default('cod')->after('delivery_charge');
            $table->text('order_note')->nullable()->after('payment_method');
            $table->string('promo_code')->nullable()->after('order_note');
            $table->decimal('discount_amount', 8, 2)->default(0)->after('promo_code');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'address_type', 'division', 'district', 'thana', 'landmark',
                'delivery_option', 'delivery_charge', 'payment_method',
                'order_note', 'promo_code', 'discount_amount',
            ]);
        });
    }
};
