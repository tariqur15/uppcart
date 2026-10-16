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
        Schema::create('popup_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('popup_id');
            $table->string('type'); // impression | view | click | close
            $table->string('page')->nullable();
            $table->string('device')->nullable();
            $table->timestamps();

            $table->foreign('popup_id')->references('id')->on('popups')->cascadeOnDelete();
            $table->index(['popup_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('popup_events');
    }
};
