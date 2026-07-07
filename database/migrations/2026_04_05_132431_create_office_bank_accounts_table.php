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
        Schema::create('office_bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('office_id');
            $table->text('bank')->nullable();
            $table->string('currency', 3)->nullable();
            $table->string('account_number', 50)->nullable();
            $table->string('iban', 50)->nullable();
            $table->string('swift', 50)->nullable();
            $table->boolean('is_main_account')->default(false);
            $table->timestamps();

            // $table->foreign('office_id')->references('id')->on('offices')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('office_bank_accounts');
    }
};
