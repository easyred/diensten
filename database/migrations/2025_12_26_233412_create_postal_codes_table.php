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
        Schema::create('postal_codes', function (Blueprint $table) {
            $table->id(); // bigint(20) UNSIGNED NOT NULL
            $table->string('Postcode', 10)->nullable(); // varchar(10) DEFAULT NULL
            $table->string('Plaatsnaam_NL', 255)->nullable(); // varchar(255) DEFAULT NULL
            $table->string('Plaatsnaam_FR', 255)->nullable(); // varchar(255) DEFAULT NULL
            $table->string('Plaatsnaam_EN', 255)->nullable(); // varchar(255) DEFAULT NULL
            $table->string('Deelgemeente', 255)->nullable(); // varchar(255) DEFAULT NULL
            $table->string('Provincie', 255)->nullable(); // varchar(255) DEFAULT NULL
            $table->string('Latitude', 255)->nullable(); // varchar(255) DEFAULT NULL
            $table->string('Longitude', 255)->nullable(); // varchar(255) DEFAULT NULL
            $table->string('Hoofdgemeente', 255)->nullable(); // varchar(255) DEFAULT NULL
            $table->timestamps(); // created_at and updated_at timestamps
            
            // Add indexes for common queries (these won't affect data import)
            $table->index('Postcode');
            $table->index('Plaatsnaam_NL');
            $table->index('Hoofdgemeente');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('postal_codes');
    }
};
