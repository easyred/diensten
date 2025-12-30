<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WaFlow;
use App\Models\Category;
use Illuminate\Http\Request;

class WaFlowController extends Controller
{
    public function index(Request $request)
    {
        $categoryId = $request->get('category_id');
        
        $query = WaFlow::with('category');
        
        // Filter by category if provided
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }
        
        $flows = $query->orderBy('name')->paginate(15);
        $categories = Category::where('is_active', true)->get();
        
        return view('admin.flows.index', compact('flows', 'categories', 'categoryId'));
    }

    public function create(Request $request)
    {
        $categoryId = $request->get('category_id');
        $flow = new WaFlow(['category_id' => $categoryId]);
        $categories = Category::where('is_active', true)->get();
        
        return view('admin.flows.create', compact('flow', 'categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'code' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'entry_keyword' => 'nullable|string|max:255',
            'target_role' => 'nullable|in:client,plumber,gardener,any',
            'is_active' => 'boolean',
        ]);

        // Check unique code per category
        $exists = WaFlow::where('category_id', $data['category_id'] ?? null)
            ->where('code', $data['code'])
            ->exists();
            
        if ($exists) {
            return back()->withInput()->withErrors(['code' => 'This code already exists for this category.']);
        }

        $data['is_active'] = $request->boolean('is_active');
        $data['target_role'] = $data['target_role'] ?? 'any';

        WaFlow::create($data);

        return redirect()->route('admin.flows.index', ['category_id' => $data['category_id']])
            ->with('success', 'Flow created.');
    }

    public function edit(WaFlow $flow)
    {
        $categories = Category::where('is_active', true)->get();
        $flow->load('category');
        return view('admin.flows.edit', compact('flow', 'categories'));
    }

    public function update(Request $request, WaFlow $flow)
    {
        $data = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'code' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'entry_keyword' => 'nullable|string|max:255',
            'target_role' => 'nullable|in:client,plumber,gardener,any',
            'is_active' => 'boolean',
        ]);

        // Check unique code per category (excluding current flow)
        $exists = WaFlow::where('category_id', $data['category_id'] ?? null)
            ->where('code', $data['code'])
            ->where('id', '!=', $flow->id)
            ->exists();
            
        if ($exists) {
            return back()->withInput()->withErrors(['code' => 'This code already exists for this category.']);
        }

        $data['is_active'] = $request->boolean('is_active');
        $data['target_role'] = $data['target_role'] ?? 'any';

        $flow->update($data);

        return redirect()->route('admin.flows.index', ['category_id' => $data['category_id']])
            ->with('success', 'Flow updated.');
    }

    public function destroy(WaFlow $flow)
    {
        $categoryId = $flow->category_id;
        $flow->delete();
        
        return redirect()->route('admin.flows.index', ['category_id' => $categoryId])
            ->with('success', 'Flow deleted.');
    }
}

