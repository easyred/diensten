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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // e.g., 'plumber', 'gardener'
            $table->string('name');           // e.g., 'Plumber Service', 'Gardening Service'
            $table->string('logo_url')->nullable();
            $table->string('domain')->nullable(); // e.g., 'plumber.example.com'
            $table->boolean('is_active')->default(true);
            $table->json('config')->nullable(); // For dynamic provider-specific config
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
