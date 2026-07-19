<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crrs', function (Blueprint $table) {
            $table->foreignId('registered_by')
                ->nullable()
                ->after('stock_number')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('crrs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('registered_by');
        });
    }
};
