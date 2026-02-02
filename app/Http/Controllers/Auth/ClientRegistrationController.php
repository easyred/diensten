<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class ClientRegistrationController extends Controller
{
    /**
     * Display the client registration view.
     */
    public function create(): View
    {
        return view('auth.client-register');
    }

    /**
     * Handle an incoming client registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Honeypot spam protection
        if ($request->filled('website')) {
            // Bot detected, silently fail
            return redirect()->route('client.register');
        }

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
            'address_json' => ['nullable', 'array'],
        ]);

        // Normalize WhatsApp number (remove non-digits except +)
        $whatsappNumber = preg_replace('/[^\d+]/', '', $request->whatsapp_number);
        // If it doesn't start with +, ensure it's properly formatted
        if (!str_starts_with($whatsappNumber, '+')) {
            // Remove leading zeros and add country code if needed
            $whatsappNumber = ltrim($whatsappNumber, '0');
            if (!str_starts_with($whatsappNumber, '+')) {
                $whatsappNumber = '+32' . $whatsappNumber; // Default to Belgium
            }
        }

        // Create user with 'client' role
        $user = User::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'company_name' => $request->company_name,
            'whatsapp_number' => $whatsappNumber,
            'phone' => $whatsappNumber, // Use WhatsApp number as phone
            'address' => $request->address,
            'number' => $request->number,
            'postal_code' => $request->postal_code,
            'city' => $request->city,
            'country' => 'Belgium',
            'address_json' => $request->address_json,
            'role' => 'client',
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}

