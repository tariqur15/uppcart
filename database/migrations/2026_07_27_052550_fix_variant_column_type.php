<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // No schema change — existing json column with array cast works correctly for variant strings.
    }

    public function down(): void
    {
        //
    }
};
