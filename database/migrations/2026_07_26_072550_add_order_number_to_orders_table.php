<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('orders', 'order_number')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('order_number')->nullable()->after('id');
            });
        }

        $orders = DB::table('orders')->whereNull('order_number')->orWhere('order_number', '')->orderBy('id')->get();
        foreach ($orders as $order) {
            $date = $order->created_at ? Carbon::parse($order->created_at) : now();
            $ddmmyy = $date->format('dmy');
            DB::table('orders')->where('id', $order->id)->update([
                'order_number' => 'ORD-'.$ddmmyy.'-'.str_pad($order->id, 6, '0', STR_PAD_LEFT),
            ]);
        }

        $driver = DB::connection()->getDriverName();
        if ($driver === 'mysql') {
            $hasUnique = DB::select("SHOW INDEX FROM orders WHERE Column_name = 'order_number' AND Key_name != 'PRIMARY'");
            if (empty($hasUnique)) {
                Schema::table('orders', function (Blueprint $table) {
                    $table->unique('order_number');
                });
            }

            DB::statement('ALTER TABLE orders MODIFY order_number VARCHAR(255) NOT NULL');
        } else {
            Schema::table('orders', function (Blueprint $table) {
                $table->unique('order_number');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('order_number');
        });
    }
};
