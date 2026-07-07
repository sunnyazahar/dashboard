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
        Schema::table('hubs', function (Blueprint $table) {
            $table->boolean('scangun_photo_taking')->default(false)->after('scan_gun_password');
            $table->boolean('scangun_detailed_shipment_out')->default(false)->after('scangun_photo_taking');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hubs', function (Blueprint $table) {
            $table->dropColumn(['scangun_photo_taking', 'scangun_detailed_shipment_out']);
        });
    }
};
