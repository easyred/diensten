<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class SiteConfigController extends Controller
{
    /**
     * Get site configuration for a specific category by code
     */
    public function show($code)
    {
        $category = Category::where('code', $code)->firstOrFail();

        if (!$category->is_active) {
            return response()->json(['error' => 'Category is not active'], 404);
        }

        return response()->json($this->formatConfig($category));
    }

    /**
     * Get all active site configurations
     */
    public function index()
    {
        $categories = Category::where('is_active', true)
            ->whereNotNull('domain')
            ->get();

        $configs = $categories->map(function ($category) {
            return $this->formatConfig($category);
        });

        return response()->json(['categories' => $configs]);
    }

    /**
     * Format category data for site configuration
     */
    protected function formatConfig(Category $category)
    {
        $hubUrl = config('app.url');

        return [
            'code' => $category->code,
            'name' => $category->name,
            'domain' => $category->domain,
            'logo' => [
                'url' => $category->logo_url ? url($category->logo_url) : null,
                'alt' => $category->name . ' Logo',
            ],
            'favicon' => [
                'url' => $category->favicon_url ? url($category->favicon_url) : null,
            ],
            'meta' => [
                'title' => $category->meta_title ?: $category->name . ' | ' . config('app.name'),
                'description' => $category->meta_description ?: $category->site_description,
                'keywords' => $category->meta_keywords,
                'og_image' => $category->og_image_url ? url($category->og_image_url) : null,
            ],
            'branding' => [
                'primary_color' => $category->primary_color ?: '#0066cc',
                'secondary_color' => $category->secondary_color ?: '#00cc66',
                'site_description' => $category->site_description,
            ],
            'redirects' => [
                'register' => $hubUrl . '/register?category=' . $category->code,
                'login' => $hubUrl . '/login',
                'dashboard' => $hubUrl . '/service-provider/dashboard',
                'client_dashboard' => $hubUrl . '/client/dashboard',
            ],
            'hub_url' => $hubUrl,
            'is_active' => $category->is_active,
            'last_deployed_at' => $category->last_deployed_at?->toISOString(),
            'deploy_status' => $category->deploy_status,
        ];
    }
}
