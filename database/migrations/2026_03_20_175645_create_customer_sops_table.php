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
        Schema::create('customer_sops', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->unique();
            $table->string('send_stocklist')->nullable();
            $table->string('onboard_delivery')->nullable();
            $table->string('quotes_prior_to_instructions')->nullable();
            $table->string('agreed_rate')->nullable();
            $table->string('invoicing_procedure')->nullable();
            $table->string('pending_entry')->nullable();
            $table->string('special_pending_routines')->nullable();
            $table->text('other_procedures_comments')->nullable();
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_sops');
    }
};
