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
        Schema::table('abandoned_carts', function (Blueprint $table) {
            $table->string('reminder_type', 20)->nullable()->index();
            $table->timestamp('last_reminder_sent_at')->nullable();
            $table->unsignedTinyInteger('reminder_sent_count')->default(0);
            $table->timestamp('ignored_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('abandoned_carts', function (Blueprint $table) {
            $table->dropColumn(['reminder_type', 'last_reminder_sent_at', 'reminder_sent_count', 'ignored_at']);
        });
    }
};
