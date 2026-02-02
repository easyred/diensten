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
        Schema::table('categories', function (Blueprint $table) {
            $table->integer('menu_order')->nullable()->after('is_active');
            $table->json('domains')->nullable()->after('domain'); // Multiple domains support
            $table->string('default_urgency')->nullable()->after('config');
            $table->json('flow_choices')->nullable()->after('config'); // Store choices for flow generation
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['menu_order', 'domains', 'default_urgency', 'flow_choices']);
        });
    }
};

