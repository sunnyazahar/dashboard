<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crr_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crr_id')->constrained('crrs')->onDelete('cascade');
            $table->string('type')->nullable();
            $table->string('carrier')->nullable();
            $table->decimal('net_value', 15, 2)->nullable();
            $table->string('currency')->nullable();
            $table->decimal('net_value_usd', 15, 2)->nullable();
            $table->string('invoice_no')->nullable();
            $table->text('remarks')->nullable();
            $table->string('hub_agent')->nullable();
            $table->string('tag')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crr_costs');
    }
};
