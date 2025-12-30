<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategorySiteConfigController extends Controller
{
    /**
     * Show the site configuration form for a category
     */
    public function show(Category $category)
    {
        return view('admin.categories.site-config', compact('category'));
    }

    /**
     * Update the site configuration for a category
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'domain' => 'nullable|string|max:255',
            'site_description' => 'nullable|string|max:1000',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'primary_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'secondary_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'favicon' => 'nullable|image|mimes:ico,png,jpg|max:512',
            'og_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->only([
            'domain',
            'site_description',
            'meta_title',
            'meta_description',
            'meta_keywords',
            'primary_color',
            'secondary_color',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logoPath = $this->uploadFile($request->file('logo'), 'logos', $category->code);
            $data['logo_url'] = Storage::url($logoPath);
        }

        // Handle favicon upload
        if ($request->hasFile('favicon')) {
            $faviconPath = $this->uploadFile($request->file('favicon'), 'favicons', $category->code);
            $data['favicon_url'] = Storage::url($faviconPath);
        }

        // Handle OG image upload
        if ($request->hasFile('og_image')) {
            $ogImagePath = $this->uploadFile($request->file('og_image'), 'og', $category->code);
            $data['og_image_url'] = Storage::url($ogImagePath);
        }

        // Update category
        $category->update($data);

        // Set deploy status to pending
        $category->update([
            'deploy_status' => 'pending',
        ]);

        return redirect()
            ->route('admin.categories.site-config.show', $category)
            ->with('success', 'Site configuration updated successfully! Deployment status set to pending.');
    }

    /**
     * Upload a file and return the storage path
     */
    protected function uploadFile($file, $directory, $categoryCode)
    {
        $filename = $categoryCode . '_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs("public/{$directory}", $filename);
        return $path;
    }

    /**
     * Remove logo
     */
    public function removeLogo(Category $category)
    {
        if ($category->logo_url) {
            $path = str_replace('/storage/', 'public/', $category->logo_url);
            Storage::delete($path);
            $category->update(['logo_url' => null]);
        }

        return back()->with('success', 'Logo removed successfully');
    }

    /**
     * Remove favicon
     */
    public function removeFavicon(Category $category)
    {
        if ($category->favicon_url) {
            $path = str_replace('/storage/', 'public/', $category->favicon_url);
            Storage::delete($path);
            $category->update(['favicon_url' => null]);
        }

        return back()->with('success', 'Favicon removed successfully');
    }

    /**
     * Remove OG image
     */
    public function removeOgImage(Category $category)
    {
        if ($category->og_image_url) {
            $path = str_replace('/storage/', 'public/', $category->og_image_url);
            Storage::delete($path);
            $category->update(['og_image_url' => null]);
        }

        return back()->with('success', 'OG image removed successfully');
    }

    /**
     * Mark deployment as successful (called by VPS script)
     */
    public function markDeployed(Category $category)
    {
        $category->update([
            'deploy_status' => 'success',
            'last_deployed_at' => now(),
        ]);

        return response()->json(['message' => 'Deployment status updated']);
    }
}
