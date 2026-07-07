<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('shipment_number')->unique();

            $table->string('departure')->nullable();
            $table->string('departure_port_code')->nullable();
            $table->string('service')->nullable();
            $table->date('preferred_shipment_date')->nullable();
            $table->date('deadline_arrival')->nullable();
            $table->date('vessel_eta')->nullable();
            $table->date('vessel_etd')->nullable();
            $table->date('pre_alert_reminder')->nullable();
            $table->string('customer_reference')->nullable();
            $table->boolean('not_applicable_for_consolidation')->default(false);

            $table->string('consignee')->nullable();
            $table->text('consignee_address')->nullable();
            $table->string('consignee_city')->nullable();
            $table->string('consignee_district')->nullable();
            $table->string('consignee_zip')->nullable();
            $table->string('consignee_country')->nullable();
            $table->string('consignee_port_code')->nullable();
            $table->string('consignee_att')->nullable();
            $table->string('consignee_email')->nullable();

            $table->unsignedBigInteger('account_manager_id')->nullable();
            $table->text('special_considerations_destination')->nullable();
            $table->boolean('skip_instruction_dest')->default(false);
            $table->text('comments_departure_hub')->nullable();
            $table->boolean('skip_instruction_hub')->default(false);
            $table->text('comments_consignee')->nullable();
            $table->boolean('skip_prealert')->default(false);
            $table->boolean('project_logistics')->default(false);
            $table->boolean('port_agency')->default(false);

            $table->string('status')->default('Draft');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('shipment_crr', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('crr_id')->constrained('crrs')->cascadeOnDelete();
            $table->unique(['shipment_id', 'crr_id']);
        });

        Schema::create('shipment_irregularities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained()->cascadeOnDelete();
            $table->date('irregularity_date')->nullable();
            $table->string('irregularity_type')->nullable();
            $table->string('party_responsible')->nullable();
            $table->string('consequence')->nullable();
            $table->decimal('extra_cost_mt_usd', 12, 2)->nullable();
            $table->string('status')->nullable();
            $table->text('cause_of_irregularity')->nullable();
            $table->text('action_taken')->nullable();
            $table->text('customer_response')->nullable();
            $table->text('hub_agent_comments')->nullable();
            $table->string('handled_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_irregularities');
        Schema::dropIfExists('shipment_crr');
        Schema::dropIfExists('shipments');
    }
};
