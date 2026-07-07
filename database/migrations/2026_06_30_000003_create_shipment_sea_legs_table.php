<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipment_sea_legs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained()->cascadeOnDelete();
            $table->string('bill_of_lading')->nullable();
            $table->string('container_number')->nullable();
            $table->string('transport_vessel_imo')->nullable();
            $table->string('transport_vessel_name')->nullable();
            $table->date('etd')->nullable();
            $table->date('eta')->nullable();
            $table->string('arrival_time', 5)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_sea_legs');
    }
};
