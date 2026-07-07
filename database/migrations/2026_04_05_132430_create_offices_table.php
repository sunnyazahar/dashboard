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
        Schema::create('offices', function (Blueprint $table) {
            $table->id();
            // Pillar 1: Office info
            $table->string('office_name', 150);
            $table->string('office_short_name', 50)->nullable();
            $table->string('phone_number', 50)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('eori_number', 50)->nullable();

            // Pillar 2: Main address
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('district_state', 100)->nullable();
            $table->string('zip_code', 20)->nullable();
            $table->unsignedBigInteger('country_id')->nullable();

            // Optional postal address
            $table->text('postal_address')->nullable();
            $table->string('postal_city', 100)->nullable();
            $table->string('postal_district_state', 100)->nullable();
            $table->string('postal_zip_code', 20)->nullable();
            $table->unsignedBigInteger('office_country_id')->nullable();

            // Pillar 3: Billing details
            $table->string('invoicing_currency', 3)->nullable();
            $table->string('reporting_currency', 3)->nullable();
            $table->string('vat_rates')->nullable();
            $table->string('vat_country_specific_name')->nullable();
            $table->string('vat_number', 50)->nullable();
            $table->string('invoicing_emails')->nullable();
            $table->string('heading_invoice')->nullable();
            $table->text('information_invoice')->nullable();

            // Checkboxes
            $table->boolean('use_vat_check')->default(false);
            $table->boolean('show_imo')->default(false);
            $table->boolean('enable_reader')->default(false);

            $table->timestamps();

            // Foreign keys (optional but recommended if tables exist)
            // $table->foreign('country_id')->references('id')->on('countries');
            // $table->foreign('office_country_id')->references('id')->on('countries');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offices');
    }
};
