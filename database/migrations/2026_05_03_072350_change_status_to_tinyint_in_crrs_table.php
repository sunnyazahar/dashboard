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
        // Update existing string values to numeric placeholders before changing type
        DB::table('crrs')->where('status', 'Pending')->update(['status' => '0']);
        DB::table('crrs')->where('status', 'Stock')->update(['status' => '1']);
        DB::table('crrs')->where('status', 'On call')->update(['status' => '2']);

        Schema::table('crrs', function (Blueprint $table) {
            $table->tinyInteger('status')->unsigned()->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('crrs', function (Blueprint $table) {
            $table->string('status')->default('Pending')->change();
        });

        // Revert numeric values back to strings
        DB::table('crrs')->where('status', '0')->update(['status' => 'Pending']);
        DB::table('crrs')->where('status', '1')->update(['status' => 'Stock']);
        DB::table('crrs')->where('status', '2')->update(['status' => 'On call']);
    }
};
