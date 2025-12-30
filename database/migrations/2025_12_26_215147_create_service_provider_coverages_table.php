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
        Schema::create('service_provider_coverages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('hoofdgemeente', 255)->index();
            $table->string('city')->nullable();
            $table->enum('coverage_type', ['municipality', 'city'])->default('municipality');
            $table->timestamps();
            
            $table->index(['user_id', 'hoofdgemeente']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_provider_coverages');
    }
};
