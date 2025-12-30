<?php

namespace App\Http\Controllers\ServiceProvider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;

class CategoryController extends Controller
{

    public function edit()
    {
        $user = Auth::user();
        abort_unless($user && in_array($user->role, ['plumber', 'gardener']), 403);

        // Get all active service provider categories (plumber, gardener, etc.)
        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get();

        // Get the categories this user is associated with
        $selected = $user->categories()->pluck('categories.id')->toArray();

        return view('service-provider.categories.edit', compact('categories', 'selected'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        abort_unless($user && in_array($user->role, ['plumber', 'gardener']), 403);

        $data = $request->validate([
            'categories'   => 'array',
            'categories.*' => 'integer|exists:categories,id',
        ]);

        // sync selections (empty array = remove all)
        $user->categories()->sync($data['categories'] ?? []);

        return redirect()
            ->route('service-provider.categories.edit')
            ->with('success', 'Categories updated successfully.');
    }
}
