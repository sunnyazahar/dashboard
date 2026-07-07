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
            // Billing & Invoicing
            $table->string('invoicing_name')->nullable()->after('portal_email');
            $table->text('invoicing_address')->nullable()->after('invoicing_name');
            $table->string('invoicing_city')->nullable()->after('invoicing_address');
            $table->string('invoicing_district')->nullable()->after('invoicing_city');
            $table->string('invoicing_zip')->nullable()->after('invoicing_district');
            $table->string('billing_country')->nullable()->after('invoicing_zip');
            $table->string('emails_for_invoicing')->nullable()->after('billing_country');
            $table->string('emails_for_invoicing_cc')->nullable()->after('emails_for_invoicing');
            $table->string('vat_number')->nullable()->after('emails_for_invoicing_cc');
            $table->string('invoicing_frequency')->nullable()->after('vat_number');

            // Pricing & Payment
            $table->string('billing_currency_outgoing')->nullable()->after('invoicing_frequency');
            $table->string('payment_terms_outgoing')->nullable()->after('billing_currency_outgoing');
            $table->string('billing_currency_incoming')->nullable()->after('payment_terms_outgoing');
            $table->string('payment_terms_incoming')->nullable()->after('billing_currency_incoming');
            $table->string('agreement_type')->nullable()->after('payment_terms_incoming');
            $table->decimal('rebate_percentage', 8, 2)->nullable()->after('agreement_type');

            // Email Settings
            $table->text('export_services')->nullable()->after('rebate_percentage');
            $table->text('import_services')->nullable()->after('export_services');
            $table->text('export_emails')->nullable()->after('import_services');
            $table->text('import_emails')->nullable()->after('export_emails');
            $table->text('stock_item_changed_emails')->nullable()->after('import_emails');
            $table->text('quote_requests_emails')->nullable()->after('stock_item_changed_emails');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hubs', function (Blueprint $table) {
            $table->dropColumn([
                'invoicing_name', 'invoicing_address', 'invoicing_city', 'invoicing_district', 'invoicing_zip', 'billing_country',
                'emails_for_invoicing', 'emails_for_invoicing_cc', 'vat_number', 'invoicing_frequency',
                'billing_currency_outgoing', 'payment_terms_outgoing', 'billing_currency_incoming', 'payment_terms_incoming',
                'agreement_type', 'rebate_percentage',
                'export_services', 'import_services', 'export_emails', 'import_emails',
                'stock_item_changed_emails', 'quote_requests_emails'
            ]);
        });
    }
};
