<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crr_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crr_id')->constrained('crrs')->onDelete('cascade');
            $table->decimal('length', 10, 2)->nullable();
            $table->decimal('width', 10, 2)->nullable();
            $table->decimal('height', 10, 2)->nullable();
            $table->decimal('weight', 10, 2)->nullable();
            $table->decimal('cbm', 10, 4)->nullable();
            $table->string('warehouse_location')->nullable();
            $table->boolean('is_dgr')->default(false);
            $table->boolean('is_not_stackable')->default(false);
            $table->boolean('is_medicine')->default(false);
            $table->boolean('is_xray')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crr_packages');
    }
};
