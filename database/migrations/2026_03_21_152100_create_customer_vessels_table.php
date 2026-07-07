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
        Schema::create('customer_vessels', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');

            // Vessel information
            $table->string('vessel');
            $table->string('vessel_name_alias')->nullable();
            $table->string('vessel_imo')->nullable();
            $table->string('shipyard')->nullable();
            $table->string('shipyard_location')->nullable();
            $table->boolean('not_in_transit')->default(false);
            $table->boolean('inactive_vessel')->default(false);
            $table->boolean('sanction_blocked')->default(false);
            $table->boolean('financially_blocked')->default(false);
            $table->boolean('pre_payment_only')->default(false);
            $table->string('customer_vessel_code')->nullable();
            $table->string('vessel_type_alias')->nullable();
            $table->string('po_example')->nullable();
            $table->string('internal_shipment')->nullable();
            $table->string('except_from_hubs')->nullable();
            $table->text('remarks')->nullable();

            // Responsible managers
            $table->string('manager')->nullable();
            $table->string('account_manager')->nullable();
            $table->string('receivers_stocklists')->nullable();

            // Invoice details
            $table->boolean('invoice_vessel_separately')->default(false);
            $table->string('title_invoice_recipient')->nullable();
            $table->string('yearly_customer_reference')->nullable();

            // Home ports
            $table->string('home_consolidation_port')->nullable();
            $table->string('home_delivery_port')->nullable();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_vessels');
    }
};
