<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipment_stock_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained()->cascadeOnDelete();
            $table->string('shipment_number')->index();
            $table->unsignedBigInteger('original_crr_id')->nullable()->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('hub_code')->nullable();
            $table->string('vessel_name')->nullable();
            $table->json('po_numbers')->nullable();
            $table->string('supplier')->nullable();
            $table->string('stock_number')->nullable();
            $table->unsignedInteger('pieces_count')->default(0);
            $table->decimal('total_weight', 12, 2)->default(0);
            $table->decimal('total_cbm', 12, 4)->default(0);
            $table->decimal('customs_value', 15, 2)->nullable();
            $table->string('currency')->nullable();
            $table->string('status_label')->nullable();
            $table->json('snapshot_data');
            $table->timestamps();

            $table->index(['shipment_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_stock_snapshots');
    }
};
