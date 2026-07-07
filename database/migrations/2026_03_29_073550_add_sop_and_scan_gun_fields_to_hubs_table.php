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
            // SOP details
            $table->boolean('coc_signed')->default(false)->after('quote_requests_emails');
            $table->boolean('sop_implemented')->default(false)->after('coc_signed');
            $table->date('coc_signed_date')->nullable()->after('sop_implemented');
            $table->string('responsible_manager')->nullable()->after('coc_signed_date');

            // Scan gun
            $table->string('scan_gun_login')->nullable()->after('responsible_manager');
            $table->string('scan_gun_password')->nullable()->after('scan_gun_login');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hubs', function (Blueprint $table) {
            $table->dropColumn([
                'coc_signed', 'sop_implemented', 'coc_signed_date', 'responsible_manager',
                'scan_gun_login', 'scan_gun_password'
            ]);
        });
    }
};
