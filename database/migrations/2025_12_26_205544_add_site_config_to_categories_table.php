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
            // Site configuration fields
            $table->string('favicon_url')->nullable()->after('logo_url');
            $table->text('site_description')->nullable()->after('domain');
            
            // Meta tags
            $table->string('meta_title')->nullable()->after('site_description');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->string('meta_keywords')->nullable()->after('meta_description');
            $table->string('og_image_url')->nullable()->after('meta_keywords');
            
            // Branding
            $table->string('primary_color')->nullable()->after('og_image_url');
            $table->string('secondary_color')->nullable()->after('primary_color');
            
            // Deployment tracking
            $table->timestamp('last_deployed_at')->nullable()->after('secondary_color');
            $table->enum('deploy_status', ['pending', 'success', 'failed'])->nullable()->after('last_deployed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn([
                'favicon_url',
                'site_description',
                'meta_title',
                'meta_description',
                'meta_keywords',
                'og_image_url',
                'primary_color',
                'secondary_color',
                'last_deployed_at',
                'deploy_status',
            ]);
        });
    }
};
