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
        Schema::create('customer_notification_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->unique();
            $table->string('notify_stock_items')->nullable();
            $table->boolean('send_automatic_first_mile_email')->default(false);
            $table->string('notify_first_mile_email_sent')->nullable();
            $table->integer('shipping_free_storage_days')->nullable();
            $table->decimal('shipping_free_storage_weight', 12, 2)->nullable();
            $table->decimal('shipping_free_storage_volume', 12, 2)->nullable();
            $table->string('notify_free_storage_exceeded')->nullable();
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_notification_settings');
    }
};
