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
            $table->string('dgr_description')->nullable()->after('is_dgr');
            $table->string('un_number')->nullable()->after('dgr_description');
            $table->string('dgr_class')->nullable()->after('un_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('crr_packages', function (Blueprint $table) {
            $table->dropColumn(['dgr_description', 'un_number', 'dgr_class']);
        });
    }
};
