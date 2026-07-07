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
        Schema::table('crrs', function (Blueprint $table) {
            $table->string('landed_from_vessel')->nullable()->after('is_landed_goods');
        });
    }
 
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('crrs', function (Blueprint $table) {
            $table->dropColumn('landed_from_vessel');
        });
    }
};
