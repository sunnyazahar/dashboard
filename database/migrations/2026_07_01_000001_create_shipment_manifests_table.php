<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipment_manifests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('version');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('form_hash', 64)->nullable();
            $table->timestamps();

            $table->unique(['shipment_id', 'version']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_manifests');
    }
};
