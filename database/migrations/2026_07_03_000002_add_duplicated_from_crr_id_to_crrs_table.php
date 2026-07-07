<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crrs', function (Blueprint $table) {
            $table->unsignedBigInteger('duplicated_from_crr_id')->nullable()->after('stock_number')->index();
        });
    }

    public function down(): void
    {
        Schema::table('crrs', function (Blueprint $table) {
            $table->dropColumn('duplicated_from_crr_id');
        });
    }
};
