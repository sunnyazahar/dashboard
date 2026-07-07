<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('category')->nullable()->after('office_id');
        });

        // Update existing office contacts to 'operations' category
        DB::table('contacts')
            ->whereNotNull('office_id')
            ->whereNull('category')
            ->update(['category' => 'operations']);
    }

    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
};
