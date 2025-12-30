<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ServiceProviderController extends Controller
{
    /**
     * Display a listing of service providers (plumbers/gardeners)
     */
    public function index(Request $request)
    {
        $categoryId = $request->query('category_id');
        
        $query = User::whereIn('role', ['plumber', 'gardener'])
            ->with('categories');
        
        if ($categoryId) {
            $query->whereHas('categories', function($q) use ($categoryId) {
                $q->where('categories.id', $categoryId);
            });
        }
        
        $serviceProviders = $query->latest()->paginate(20);
        $categories = Category::where('is_active', true)->get();
        
        return view('admin.service-providers.index', compact('serviceProviders', 'categories', 'categoryId'));
    }

    /**
     * Show the form for creating a new service provider
     */
    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view('admin.service-providers.create', compact('categories'));
    }

    /**
     * Store a newly created service provider
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'whatsapp_number' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'city' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'btw_number' => ['nullable', 'string', 'max:255'],
            'categories' => ['required', 'array', 'min:1'],
            'categories.*' => ['exists:categories,id'],
        ]);

        // Determine role based on selected categories
        $role = 'plumber'; // default
        $categoryCodes = Category::whereIn('id', $data['categories'])->pluck('code')->toArray();
        if (in_array('gardener', $categoryCodes)) {
            $role = 'gardener';
        } elseif (in_array('plumber', $categoryCodes)) {
            $role = 'plumber';
        }

        // Normalize WhatsApp number
        $whatsappNumber = null;
        if (!empty($data['whatsapp_number'])) {
            $whatsappNumber = preg_replace('/\D/', '', $data['whatsapp_number']);
        }

        // Create user
        $user = User::create([
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'whatsapp_number' => $whatsappNumber,
            'address' => $data['address'] ?? null,
            'postal_code' => $data['postal_code'] ?? null,
            'city' => $data['city'] ?? null,
            'country' => $data['country'] ?? null,
            'company_name' => $data['company_name'] ?? null,
            'btw_number' => $data['btw_number'] ?? null,
            'role' => $role,
            'password' => Hash::make(Str::random(16)),
        ]);

        // Attach categories
        $user->categories()->attach($data['categories']);

        return redirect()->route('admin.service-providers.index')
            ->with('success', 'Service provider created successfully.');
    }

    /**
     * Display the specified service provider
     */
    public function show(User $serviceProvider)
    {
        abort_unless(in_array($serviceProvider->role, ['plumber', 'gardener']), 404);
        $serviceProvider->load('categories');
        return view('admin.service-providers.show', compact('serviceProvider'));
    }

    /**
     * Show the form for editing the specified service provider
     */
    public function edit(User $serviceProvider)
    {
        abort_unless(in_array($serviceProvider->role, ['plumber', 'gardener']), 404);
        $serviceProvider->load('categories');
        $categories = Category::where('is_active', true)->get();
        return view('admin.service-providers.edit', compact('serviceProvider', 'categories'));
    }

    /**
     * Update the specified service provider
     */
    public function update(Request $request, User $serviceProvider)
    {
        abort_unless(in_array($serviceProvider->role, ['plumber', 'gardener']), 404);
        
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($serviceProvider->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'whatsapp_number' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'city' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'btw_number' => ['nullable', 'string', 'max:255'],
            'categories' => ['required', 'array', 'min:1'],
            'categories.*' => ['exists:categories,id'],
        ]);

        // Normalize WhatsApp number
        $whatsappNumber = null;
        if (!empty($data['whatsapp_number'])) {
            $whatsappNumber = preg_replace('/\D/', '', $data['whatsapp_number']);
        }

        // Update user
        $serviceProvider->update([
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'whatsapp_number' => $whatsappNumber,
            'address' => $data['address'] ?? null,
            'postal_code' => $data['postal_code'] ?? null,
            'city' => $data['city'] ?? null,
            'country' => $data['country'] ?? null,
            'company_name' => $data['company_name'] ?? null,
            'btw_number' => $data['btw_number'] ?? null,
        ]);

        // Update categories
        $serviceProvider->categories()->sync($data['categories']);

        return redirect()->route('admin.service-providers.index')
            ->with('success', 'Service provider updated successfully.');
    }

    /**
     * Remove the specified service provider
     */
    public function destroy(User $serviceProvider)
    {
        abort_unless(in_array($serviceProvider->role, ['plumber', 'gardener']), 404);
        $serviceProvider->delete();
        return redirect()->route('admin.service-providers.index')
            ->with('success', 'Service provider deleted successfully.');
    }
}

