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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            // Supplier information
            $table->string('supplier_name');
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->text('remarks')->nullable();
            $table->text('special_considerations')->nullable();
            
            // Supplier address
            $table->text('supplier_address')->nullable();
            $table->string('city')->nullable();
            $table->string('district_state')->nullable();
            $table->string('zip_code')->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->string('port_code')->nullable();
            
            // Office address (optional)
            $table->text('office_address')->nullable();
            $table->string('office_city')->nullable();
            $table->string('office_district_state')->nullable();
            $table->string('office_zip_code')->nullable();
            $table->unsignedBigInteger('office_country_id')->nullable();
            
            // Supplier details
            $table->string('vat_number')->nullable();
            $table->string('eori_number')->nullable();
            $table->string('currency')->nullable();
            $table->string('un_locode')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
