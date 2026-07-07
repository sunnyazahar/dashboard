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
        Schema::create('other_companies', function (Blueprint $table) {
            $table->id();
            // Company information
            $table->string('company_name');
            $table->string('company_type')->nullable();
            $table->string('code')->nullable();
            $table->string('code_description')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->text('remarks')->nullable();
            $table->text('special_considerations')->nullable();
            
            // Company address
            $table->text('street_address')->nullable();
            $table->string('city')->nullable();
            $table->string('district_state')->nullable();
            $table->string('zip_code')->nullable();
            $table->foreignId('country_id')->nullable()->constrained('countries');
            $table->string('port_code')->nullable();
            
            // Office address (Optional)
            $table->text('office_street_address')->nullable();
            $table->string('office_city')->nullable();
            $table->string('office_district_state')->nullable();
            $table->string('office_zip_code')->nullable();
            $table->foreignId('office_country_id')->nullable()->constrained('countries');
            
            // Company details
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
        Schema::dropIfExists('other_companies');
    }
};
