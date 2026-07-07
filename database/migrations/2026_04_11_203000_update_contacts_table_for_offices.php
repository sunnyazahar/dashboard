<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->foreignId('office_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('reply_to_email')->nullable();
            $table->boolean('is_cc_enabled')->default(false);
            $table->boolean('status')->default(true); // Added for 'Activated' status column
        });
    }

    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropForeign(['office_id']);
            $table->dropColumn(['office_id', 'reply_to_email', 'is_cc_enabled', 'status']);
        });
    }
};
