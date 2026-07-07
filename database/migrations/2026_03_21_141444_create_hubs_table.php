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
        Schema::create('hubs', function (Blueprint $table) {
            $table->id();
            $table->string('hub_name');
            $table->string('company_id')->nullable();
            $table->string('customer_number_fm')->nullable();
            $table->string('code')->nullable();
            $table->string('code_description')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->boolean('is_gts_company')->default(false);
            $table->text('remarks')->nullable();
            $table->text('special_considerations')->nullable();
            $table->boolean('show_pre_alert')->default(false);
            
            // Hub Address
            $table->text('hub_address')->nullable();
            $table->string('city')->nullable();
            $table->string('district_state')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('country')->nullable();
            $table->string('port_code')->nullable();
            
            // Office Address
            $table->text('office_address')->nullable();
            $table->string('office_city')->nullable();
            $table->string('office_district_state')->nullable();
            $table->string('office_zip_code')->nullable();
            $table->string('office_country')->nullable();
            
            // Hub Details
            $table->string('eori_number')->nullable();
            $table->string('un_locode')->nullable();
            
            // Customer Portal
            $table->boolean('hide_in_portal')->default(true);
            $table->text('portal_remarks')->nullable();
            $table->string('portal_email')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hubs');
    }
};
