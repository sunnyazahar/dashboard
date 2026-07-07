<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crrs', function (Blueprint $table) {
            $table->id();
            $table->string('vessel_name')->nullable();
            $table->json('po_numbers')->nullable();
            $table->text('po_remarks')->nullable();
            $table->string('content')->default('Shipspares');
            $table->string('first_mile_updates')->nullable();
            $table->text('first_mile_comment')->nullable();
            $table->string('supplier')->nullable();
            $table->boolean('is_landed_goods')->default(false);
            $table->string('expected_delivery_date')->nullable();
            $table->string('actual_delivery_date')->nullable();
            $table->string('supplier_reference')->nullable();
            $table->string('deadline_warehouse')->nullable();
            $table->string('internal_shipment')->nullable();
            $table->json('delivery_irregularities')->nullable();
            $table->string('hub_agent')->nullable();
            $table->string('transit_type')->nullable();
            $table->string('transit_id')->nullable();
            $table->boolean('is_bonded_goods')->default(false);
            $table->string('customs_doc_type')->nullable();
            $table->string('bonded_date')->nullable();
            $table->string('customs_doc_reference')->nullable();
            $table->string('customs_lot_number')->nullable();
            $table->string('country_of_origin')->nullable();
            $table->string('hs_code')->nullable();
            $table->string('currency')->nullable();
            $table->decimal('customs_value', 15, 2)->nullable();
            $table->string('priority')->nullable();
            $table->string('status')->default('Pending');
            $table->text('internal_comments')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crrs');
    }
};
