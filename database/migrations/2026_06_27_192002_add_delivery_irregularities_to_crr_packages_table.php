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
        Schema::table('crr_packages', function (Blueprint $table) {
            $table->boolean('is_delivery_irregularity')->default(false)->after('remarks');
            $table->json('delivery_irregularities')->nullable()->after('is_delivery_irregularity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('crr_packages', function (Blueprint $table) {
            $table->dropColumn(['is_delivery_irregularity', 'delivery_irregularities']);
        });
    }
};
