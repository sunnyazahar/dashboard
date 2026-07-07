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
        Schema::table('hubs', function (Blueprint $table) {
            $table->date('agreement_start_date')->nullable()->after('agreement_type');
            $table->date('agreement_expiry_date')->nullable()->after('agreement_start_date');
            $table->decimal('minimal_cbm', 8, 2)->nullable()->after('agreement_expiry_date');
            $table->decimal('minimal_weight', 8, 2)->nullable()->after('minimal_cbm');
            $table->integer('free_storage_days')->nullable()->after('minimal_weight');
            $table->decimal('cbm_charge_usd', 8, 2)->nullable()->after('free_storage_days');
        });

        Schema::table('hub_documents', function (Blueprint $table) {
            $table->string('document_type')->default('sop')->after('hub_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hubs', function (Blueprint $table) {
            $table->dropColumn([
                'agreement_start_date', 'agreement_expiry_date', 'minimal_cbm', 
                'minimal_weight', 'free_storage_days', 'cbm_charge_usd'
            ]);
        });

        Schema::table('hub_documents', function (Blueprint $table) {
            $table->dropColumn('document_type');
        });
    }
};
