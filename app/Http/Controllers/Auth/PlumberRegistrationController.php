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
use App\Models\PostalCode;
use App\Models\ServiceProviderSchedule;
use App\Notifications\WelcomeNotification;
use App\Services\WhatsAppService;

class PlumberRegistrationController extends Controller
{
    /**
     * Display the plumber registration view.
     */
    public function create(): View
    {
        $postalCodes = PostalCode::orderBy('Postcode')->get();
        return view('auth.plumber-register', compact('postalCodes'));
    }

    /**
     * Handle an incoming plumber registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        // Honeypot field - if filled, it's a bot
        if ($request->filled('website') || $request->filled('homepage')) {
            \Log::warning('Bot detected via honeypot', ['ip' => $request->ip()]);
            abort(403, 'Spam detected');
        }

        // Validate suspicious content in name
        $fullName = $request->input('full_name', '');
        $suspiciousPatterns = [
            '/https?:\/\//i',           // URLs
            '/www\./i',                 // www links
            '/\.(com|net|org|ru|br|cd)/i', // Domain extensions
            '/🏆|💰|🎁|🎉|💰|💵/u',      // Prize emojis
            '/claim|prize|jackpot|reward|win/i', // Prize keywords
            '/Поздравляем|акции|попытки/i', // Russian spam text
        ];
        
        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $fullName)) {
                \Log::warning('Suspicious name detected', [
                    'name' => $fullName,
                    'ip' => $request->ip(),
                    'pattern' => $pattern
                ]);
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['full_name' => 'Invalid name format. Please use only letters, numbers, and basic punctuation.']);
            }
        }

        $request->validate([
            'full_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9\s\-\'\.]+$/u'],
            'company_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'whatsapp_number' => ['required', 'string', 'max:20', 'regex:/^[0-9+\s\-\(\)]+$/'],
            'address' => ['required', 'string', 'max:255'],
            'number' => ['nullable', 'string', 'max:10'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'city' => ['nullable', 'string', 'max:255'],
            'address_json' => ['nullable', 'string'],
            'btw_number' => ['nullable', 'string', 'max:50'],
        ]);

        // Additional WhatsApp number validation - must contain at least 8 digits
        $whatsappNumber = preg_replace('/\D/', '', $request->whatsapp_number);
        if (strlen($whatsappNumber) < 8 || strlen($whatsappNumber) > 15) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['whatsapp_number' => 'Please enter a valid WhatsApp number (8-15 digits).']);
        }

        $user = User::create([
            'full_name' => $request->full_name,
            'company_name' => $request->company_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'whatsapp_number' => $whatsappNumber, // Store normalized number (digits only)
            'address' => $request->address,
            'number' => $request->number,
            'postal_code' => $request->postal_code,
            'city' => $request->city,
            'country' => 'Belgium',
            'role' => 'plumber',
            'btw_number' => $request->btw_number,
            'address_json' => $request->address_json,
            'subscription_plan' => 'basis',
            'subscription_status' => 'active',
            'subscription_ends_at' => now()->addYear(),
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Automatically create default 24-hour schedule for new service provider
        ServiceProviderSchedule::create([
            'user_id' => $user->id,
            'timezone' => 'Europe/Brussels',
            'schedule_data' => ServiceProviderSchedule::getDefaultSchedule(),
            'holidays' => [],
            'vacations' => [],
            'last_updated' => now()
        ]);

        // Automatically add user's municipality to coverage areas
        $user->addDefaultMunicipalityCoverage();

        // Send welcome notification via email
        try {
            $user->notify(new WelcomeNotification());
        } catch (\Exception $e) {
            \Log::error('Failed to send welcome email', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage()
            ]);
        }

        // Send welcome message via WhatsApp
        try {
            $whatsappService = new WhatsAppService();
            $hasActiveSubscription = $user->subscription_status === 'active' && 
                                   $user->subscription_ends_at && 
                                   $user->subscription_ends_at->isFuture();
            $whatsappService->sendWelcomeMessage($user, $hasActiveSubscription);
        } catch (\Exception $e) {
            \Log::error('Failed to send welcome WhatsApp message', [
                'user_id' => $user->id,
                'whatsapp_number' => $user->whatsapp_number,
                'error' => $e->getMessage()
            ]);
        }

        return redirect()->route('service-provider.dashboard')->with('success', 'Service provider account created successfully!');
    }
}
