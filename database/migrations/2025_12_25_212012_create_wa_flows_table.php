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
        Schema::create('wa_flows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('cascade'); // Link to category for category-specific flows
            $table->string('code'); // e.g. 'client_flow', 'plumber_welcome'
            $table->string('name');
            $table->string('entry_keyword')->nullable(); // e.g. 'info' / 'plumber' / 'menu'
            $table->enum('target_role', ['client', 'plumber', 'gardener', 'any'])->default('any');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Unique code per category (or globally if category_id is null)
            $table->unique(['category_id', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wa_flows');
    }
};
