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
        Schema::create('service_provider_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('timezone')->default('Europe/Brussels');
            $table->json('schedule_data'); // Stores the complete schedule structure
            $table->json('holidays')->nullable(); // Array of holiday dates
            $table->json('vacations')->nullable(); // Array of vacation periods
            $table->timestamp('last_updated')->nullable();
            $table->timestamps();
            
            $table->index(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_provider_schedules');
    }
};
