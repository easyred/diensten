<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OgImageController extends Controller
{
    /**
     * Generate OG image dynamically
     * 
     * @param Request $request
     * @return Response
     */
    public function generate(Request $request)
    {
        $categoryCode = $request->get('category', 'diensten');
        $color = $request->get('color', '#007bff');
        
        // Decode color if URL encoded
        $color = urldecode($color);
        
        // Get category if exists
        $category = Category::where('code', $categoryCode)->first();
        
        // Default icon (briefcase for main domain)
        $icon = 'fa-briefcase';
        $categoryName = 'diensten.pro';
        
        if ($category) {
            $categoryName = $category->name;
            // Try to get icon from category config or use default
            $config = $category->config ?? [];
            $icon = $config['icon'] ?? 'fa-briefcase';
        }
        
        // Generate SVG OG image (1200x630)
        $svg = $this->generateOgImageSvg($categoryName, $icon, $color);
        
        return response($svg, 200)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Cache-Control', 'public, max-age=3600');
    }
    
    /**
     * Generate SVG OG image
     */
    private function generateOgImageSvg(string $title, string $icon, string $color): string
    {
        // Simple briefcase icon SVG path (scaled for 1200x630)
        $iconPath = '<path d="M320 128H192c-35.3 0-64 28.7-64 64v256c0 35.3 28.7 64 64 64h448c35.3 0 64-28.7 64-64V192c0-35.3-28.7-64-64-64H448v32c0 17.7-14.3 32-32 32H320c-17.7 0-32-14.3-32-32v-32z" fill="white" opacity="0.95"/>';
        
        // Escape HTML entities in title
        $title = htmlspecialchars($title, ENT_XML1, 'UTF-8');
        
        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="630" viewBox="0 0 1200 630">
    <defs>
        <linearGradient id="bg" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" style="stop-color:{$color};stop-opacity:1" />
            <stop offset="100%" style="stop-color:{$color};stop-opacity:0.85" />
        </linearGradient>
    </defs>
    <rect width="1200" height="630" fill="url(#bg)"/>
    <g transform="translate(600, 315)">
        <g transform="translate(0, -100) scale(4)">
            {$iconPath}
        </g>
        <text x="0" y="100" text-anchor="middle" fill="white" font-family="Arial, sans-serif" font-size="72" font-weight="bold">
            {$title}
        </text>
        <text x="0" y="160" text-anchor="middle" fill="rgba(255,255,255,0.95)" font-family="Arial, sans-serif" font-size="36">
            Professionele Diensten
        </text>
    </g>
</svg>
SVG;
        
        return $svg;
    }
}

