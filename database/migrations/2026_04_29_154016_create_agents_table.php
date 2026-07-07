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
        Schema::create('agents', function (Blueprint $col) {
            $col->id();
            $col->string('agent_name');
            $col->string('company_id')->nullable();
            $col->string('code')->nullable();
            $col->string('code_description')->nullable();
            $col->string('phone')->nullable();
            $col->string('email')->nullable();
            $col->text('remarks')->nullable();
            $col->text('special_considerations')->nullable();
            $col->boolean('show_pre_alert')->default(false);
            
            // Agent Address
            $col->text('agent_address')->nullable();
            $col->string('city')->nullable();
            $col->string('district_state')->nullable();
            $col->string('zip_code')->nullable();
            $col->unsignedBigInteger('country_id')->nullable();
            $col->string('port_code')->nullable();
            
            // Office Address
            $col->text('office_address')->nullable();
            $col->string('office_city')->nullable();
            $col->string('office_district_state')->nullable();
            $col->string('office_zip_code')->nullable();
            $col->unsignedBigInteger('office_country_id')->nullable();
            
            // Details
            $col->string('eori_number')->nullable();
            $col->string('un_locode')->nullable();
            $col->string('agent_type')->nullable();
            
            $col->timestamps();
            
            $col->foreign('country_id')->references('id')->on('countries')->onDelete('set null');
            $col->foreign('office_country_id')->references('id')->on('countries')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agents');
    }
};
