<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_login_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('session_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('browser')->nullable();
            $table->string('browser_version')->nullable();
            $table->string('operating_system')->nullable();
            $table->string('device_type')->nullable();
            $table->string('screen_resolution')->nullable();
            $table->string('language')->nullable();
            $table->string('timezone')->nullable();
            $table->decimal('browser_latitude', 10, 7)->nullable();
            $table->decimal('browser_longitude', 10, 7)->nullable();
            $table->decimal('browser_location_accuracy', 10, 2)->nullable();
            $table->decimal('ip_latitude', 10, 7)->nullable();
            $table->decimal('ip_longitude', 10, 7)->nullable();
            $table->string('city')->nullable();
            $table->string('region')->nullable();
            $table->string('country')->nullable();
            $table->string('country_code', 2)->nullable();
            $table->timestamp('logged_in_at');
            $table->timestamp('logged_out_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_login_activities');
    }
};
