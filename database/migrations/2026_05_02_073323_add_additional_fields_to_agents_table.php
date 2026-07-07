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
        Schema::table('agents', function (Blueprint $col) {
            // Billing details
            $col->string('invoicing_name')->nullable();
            $col->text('billing_address')->nullable();
            $col->string('billing_city')->nullable();
            $col->string('billing_district_state')->nullable();
            $col->string('billing_zip_code')->nullable();
            $col->unsignedBigInteger('billing_country_id')->nullable();
            $col->string('invoicing_emails')->nullable();
            $col->string('invoicing_emails_cc')->nullable();
            $col->string('vat_number')->nullable();
            $col->string('invoicing_frequency')->nullable();
            $col->boolean('applies_to_rebate')->default(false);
            $col->decimal('rebate_percentage', 5, 2)->nullable();
            $col->string('outgoing_currency')->nullable();
            $col->string('outgoing_payment_terms')->nullable();
            $col->string('incoming_currency')->nullable();
            $col->string('incoming_payment_terms')->nullable();

            // SOP
            $col->boolean('coc_signed')->default(false);
            $col->boolean('sop_implemented')->default(false);
            $col->date('coc_signed_date')->nullable();
            $col->string('responsible_manager')->nullable();

            // Pricing
            $col->boolean('calculate_sell_rates')->default(false);
            $col->string('purchase_rate')->nullable();
            $col->string('sell_rate')->nullable();
            $col->string('profit')->nullable();

            // Email Settings
            $col->text('export_email_services')->nullable();
            $col->text('import_email_services')->nullable();
            $col->string('status_changed_emails')->nullable();
            $col->string('stock_item_changed_emails')->nullable();
            $col->string('quote_requests_emails')->nullable();

            // Scan gun
            $col->string('scangun_login')->nullable();
            $col->string('scangun_password')->nullable();
            $col->boolean('scangun_enable_picture')->default(false);
            $col->boolean('scangun_enable_detailed_shipment')->default(false);

            $col->foreign('billing_country_id')->references('id')->on('countries')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agents', function (Blueprint $col) {
            $col->dropForeign(['billing_country_id']);
            $col->dropColumn([
                'invoicing_name', 'billing_address', 'billing_city', 'billing_district_state', 'billing_zip_code', 'billing_country_id',
                'invoicing_emails', 'invoicing_emails_cc', 'vat_number', 'invoicing_frequency', 'applies_to_rebate', 'rebate_percentage',
                'outgoing_currency', 'outgoing_payment_terms', 'incoming_currency', 'incoming_payment_terms',
                'coc_signed', 'sop_implemented', 'coc_signed_date', 'responsible_manager',
                'calculate_sell_rates', 'purchase_rate', 'sell_rate', 'profit',
                'export_email_services', 'import_email_services', 'status_changed_emails', 'stock_item_changed_emails', 'quote_requests_emails',
                'scangun_login', 'scangun_password', 'scangun_enable_picture', 'scangun_enable_detailed_shipment'
            ]);
        });
    }
};
