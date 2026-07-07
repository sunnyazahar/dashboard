<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crrs', function (Blueprint $table) {
            $table->boolean('accept')->default(false)->after('flags');
        });
    }

    public function down(): void
    {
        Schema::table('crrs', function (Blueprint $table) {
            $table->dropColumn('accept');
        });
    }
};
