<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = [
        'offices',
        'hubs',
        'agents',
        'other_companies',
        'suppliers',
        'customers',
        'customer_vessels',
        'contacts',
        'hub_users',
        'agent_users',
    ];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($table) {
                if (! Schema::hasColumn($table, 'created_by')) {
                    $blueprint->foreignId('created_by')->nullable()->after('id')->constrained('users')->nullOnDelete();
                }
                if (! Schema::hasColumn($table, 'updated_by')) {
                    $blueprint->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
                }
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($table) {
                if (Schema::hasColumn($table, 'created_by')) {
                    $blueprint->dropConstrainedForeignId('created_by');
                }
                if (Schema::hasColumn($table, 'updated_by')) {
                    $blueprint->dropConstrainedForeignId('updated_by');
                }
            });
        }
    }
};
