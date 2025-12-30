<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Category;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function create(Request $request): View
    {
        $categories = Category::where('is_active', true)->get();
        $preselectedCategory = $request->query('category');
        return view('auth.register', compact('categories', 'preselectedCategory'));
    }

    public function store(Request $request): RedirectResponse
    {
        // Parse categories from comma-separated string
        $categoryCodes = explode(',', $request->categories ?? '');
        $categoryCodes = array_filter(array_map('trim', $categoryCodes));
        
        $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'min:6'],
            'whatsapp_number' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'number' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'categories' => ['required', 'string'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'btw_number' => ['nullable', 'string', 'max:255'],
        ]);

        // Validate categories exist
        if (empty($categoryCodes)) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['categories' => 'Selecteer minimaal één dienst.']);
        }

        $validCategories = Category::whereIn('code', $categoryCodes)->pluck('code')->toArray();
        if (count($validCategories) !== count($categoryCodes)) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['categories' => 'Een of meer geselecteerde diensten zijn ongeldig.']);
        }

        // Normalize WhatsApp number
        $whatsappNumber = preg_replace('/\D/', '', $request->whatsapp_number);

        // Determine role based on selected categories
        $role = 'client';
        if (in_array('plumber', $validCategories)) {
            $role = 'plumber';
        } elseif (in_array('gardener', $validCategories)) {
            $role = 'gardener';
        }

        $user = User::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'whatsapp_number' => $whatsappNumber,
            'address' => $request->address,
            'number' => $request->number,
            'postal_code' => $request->postal_code,
            'city' => $request->city,
            'company_name' => $request->company_name,
            'btw_number' => $request->btw_number,
            'role' => $role,
        ]);

        // Attach categories
        $categoryIds = Category::whereIn('code', $validCategories)->pluck('id');
        $user->categories()->attach($categoryIds);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
