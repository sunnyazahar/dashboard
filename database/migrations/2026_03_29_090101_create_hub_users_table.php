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
        Schema::create('hub_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hub_id')->constrained('hubs')->onDelete('cascade');
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone_number')->nullable();
            $table->boolean('show_in_scan_gun')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hub_users');
    }
};
