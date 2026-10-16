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
        Schema::create('fraud_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('phone', 20);
            $table->json('data');
            $table->json('reports')->nullable();
            $table->unsignedSmallInteger('success_ratio')->default(0);
            $table->unsignedInteger('total_parcel')->default(0);
            $table->unsignedInteger('success_parcel')->default(0);
            $table->unsignedInteger('cancelled_parcel')->default(0);
            $table->timestamps();

            $table->index('phone');
            $table->unique('order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fraud_checks');
    }
};
