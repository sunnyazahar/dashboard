<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ports', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['airport', 'seaport'])->default('airport');
            $table->string('iata_code', 3)->nullable()->comment('IATA code (airports)');
            $table->string('icao_code', 4)->nullable()->comment('ICAO code (airports)');
            $table->string('un_locode', 5)->nullable()->comment('UN/LOCODE (seaports)');
            $table->string('port_name');
            $table->string('city')->nullable();
            $table->string('country_name');
            $table->string('country_code', 2);
            $table->string('flag', 8)->nullable();
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['type', 'country_code']);
            $table->index('port_name');
            $table->index('city');
            $table->unique(['type', 'iata_code'], 'ports_type_iata_unique');
            $table->unique(['type', 'un_locode'], 'ports_type_un_locode_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ports');
    }
};
