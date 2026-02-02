<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Category;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Models\PostalCode;

class RegisterController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(Request $request): View
    {
        $postalCodes = PostalCode::orderBy('Postcode')->get();
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        
        // Handle optional category preselection from query parameter
        $category = null;
        $preselectedCategory = null;
        
        if ($request->has('category')) {
            $categoryCode = $request->query('category');
            $category = Category::where('code', $categoryCode)
                ->where('is_active', true)
                ->first();
            if ($category) {
                $preselectedCategory = $categoryCode;
            }
        }
        
        return view('auth.register', compact('postalCodes', 'categories', 'category', 'preselectedCategory'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'company_name' => ['nullable', 'string', 'max:255'],
            'whatsapp_number' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:255'],
            'number' => ['nullable', 'string', 'max:20'],
            'postal_code' => ['required', 'string', 'max:10'],
            'city' => ['required', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'btw_number' => ['nullable', 'string', 'max:255'],
            'categories' => ['required', 'array', 'min:1'],
            'categories.*' => ['string'],
            'address_json' => ['nullable', 'array'],
        ]);

        // Convert category codes to IDs
        $categoryCodes = is_array($request->categories) ? $request->categories : [];
        $categoryIds = Category::whereIn('code', $categoryCodes)
            ->where('is_active', true)
            ->pluck('id')
            ->toArray();

        if (empty($categoryIds)) {
            return back()->withErrors(['categories' => 'Please select at least one valid service category.'])->withInput();
        }

        // Determine role based on selected categories
        // Valid roles: 'client', 'plumber', 'gardener', 'admin', 'super_admin'
        $role = 'plumber'; // default for service providers
        if (in_array('gardener', $categoryCodes)) {
            $role = 'gardener';
        } elseif (in_array('plumber', $categoryCodes)) {
            $role = 'plumber';
        }
        // For other categories, default to 'plumber' as service provider role

        // Normalize WhatsApp number (remove non-digits)
        $whatsappNumber = preg_replace('/\D/', '', $request->whatsapp_number);

        // Create user
        $user = User::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'company_name' => $request->company_name,
            'whatsapp_number' => $whatsappNumber,
            'phone' => $whatsappNumber, // Use WhatsApp number as phone if phone not provided
            'address' => $request->address,
            'number' => $request->number,
            'postal_code' => $request->postal_code,
            'city' => $request->city,
            'country' => $request->country ?? 'Belgium',
            'btw_number' => $request->btw_number,
            'address_json' => $request->address_json,
            'role' => $role,
        ]);

        // Attach categories
        $user->categories()->attach($categoryIds);

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}

