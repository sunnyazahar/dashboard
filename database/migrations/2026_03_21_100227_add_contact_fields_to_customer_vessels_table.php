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
        Schema::table('customer_vessels', function (Blueprint $table) {
            $table->unsignedBigInteger('contact_id')->after('customer_id')->nullable();
            $table->boolean('contact_stocklists')->default(false);
            $table->boolean('contact_pre_alerts')->default(false);
            $table->boolean('contact_stock_notifications')->default(false);
            $table->boolean('contact_free_storage_notifications')->default(false);
            $table->boolean('contact_offers')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_vessels', function (Blueprint $table) {
            $table->dropColumn([
                'contact_id', 'contact_stocklists', 'contact_pre_alerts',
                'contact_stock_notifications', 'contact_free_storage_notifications', 'contact_offers'
            ]);
        });
    }
};
