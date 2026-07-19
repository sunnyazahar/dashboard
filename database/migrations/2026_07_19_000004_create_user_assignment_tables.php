<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_office_assignments', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('office_id')->constrained()->cascadeOnDelete();
            $table->unique(['user_id', 'office_id']);
        });

        Schema::create('user_hub_assignments', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('hub_id')->constrained()->cascadeOnDelete();
            $table->unique(['user_id', 'hub_id']);
        });

        Schema::create('user_agent_assignments', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('agent_id')->constrained()->cascadeOnDelete();
            $table->unique(['user_id', 'agent_id']);
        });

        Schema::create('user_supplier_assignments', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->unique(['user_id', 'supplier_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_supplier_assignments');
        Schema::dropIfExists('user_agent_assignments');
        Schema::dropIfExists('user_hub_assignments');
        Schema::dropIfExists('user_office_assignments');
    }
};
