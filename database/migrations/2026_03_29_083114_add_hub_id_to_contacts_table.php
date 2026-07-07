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
        Schema::table('contacts', function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable()->change();
            $table->foreignId('hub_id')->nullable()->after('customer_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropForeign(['hub_id']);
            $table->dropColumn('hub_id');
            $table->foreignId('customer_id')->nullable(false)->change();
        });
    }
};
