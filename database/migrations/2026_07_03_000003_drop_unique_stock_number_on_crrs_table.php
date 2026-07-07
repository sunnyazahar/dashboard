<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crrs', function (Blueprint $table) {
            $table->dropUnique(['stock_number']);
            $table->index('stock_number');
        });
    }

    public function down(): void
    {
        Schema::table('crrs', function (Blueprint $table) {
            $table->dropIndex(['stock_number']);
            $table->unique('stock_number');
        });
    }
};
