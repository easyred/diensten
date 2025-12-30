<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'whatsapp_number' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'number' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
        ]);

        // Normalize WhatsApp number
        $whatsappNumber = preg_replace('/\D/', '', $request->whatsapp_number);

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
            'role' => 'client',
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
