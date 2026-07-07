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
        Schema::create('crr_documents', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('crr_id')->unsigned();
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type')->default('unspecified');
            $table->timestamps();

            $table->foreign('crr_id')->references('id')->on('crrs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crr_documents');
    }
};
